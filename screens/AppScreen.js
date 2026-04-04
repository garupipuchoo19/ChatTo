import React, { useState, useEffect, useRef } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, FlatList,
  StyleSheet, Alert, ActivityIndicator, KeyboardAvoidingView,
  Platform, Modal, ScrollView, SafeAreaView
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const BASE = 'https://limegreen-elk-680782.hostingersite.com';

// ── API helper ────────────────────────────────────────────────
async function api(path, method = 'GET', body = null) {
  try {
    const opts = { method, headers: { 'Content-Type': 'application/json' }, credentials: 'include' };
    if (body) opts.body = JSON.stringify(body);
    const r = await fetch(`${BASE}/${path}`, opts);
    return await r.json();
  } catch { return { ok: false, error: 'Error de red' }; }
}

// ── Utilidades ────────────────────────────────────────────────
function fmtTime(dt) {
  if (!dt) return '';
  const d = new Date(dt), now = new Date();
  if (d.toDateString() === now.toDateString())
    return d.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
  return d.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit' });
}

function initials(name = '') { return name.charAt(0).toUpperCase(); }

// ── Componente Avatar ─────────────────────────────────────────
function Avatar({ name, type = 'personal', size = 46 }) {
  const colors = {
    personal: ['#1d4ed8', '#7c3aed'],
    group:    ['#065f46', '#0d9488'],
    ai:       ['#7c2d12', '#b45309'],
  };
  const [c1, c2] = colors[type] || colors.personal;
  return (
    <View style={[styles.avatar, { width: size, height: size, borderRadius: size / 2, backgroundColor: c1 }]}>
      <Text style={[styles.avatarTxt, { fontSize: size * 0.38 }]}>{type === 'ai' ? '🤖' : initials(name)}</Text>
    </View>
  );
}

// ══════════════════════════════════════════════════════════════
export default function AppScreen({ navigation }) {
  const [user, setUser]           = useState(null);
  const [tab, setTab]             = useState('chats');
  const [list, setList]           = useState([]);
  const [loadingList, setLoadingList] = useState(false);
  const [activeChat, setActiveChat]   = useState(null);
  const [messages, setMessages]       = useState([]);
  const [msgInput, setMsgInput]       = useState('');
  const [lastMsgId, setLastMsgId]     = useState(0);
  const [aiHistory, setAiHistory]     = useState([]);
  const [aiTyping, setAiTyping]       = useState(false);
  const [search, setSearch]           = useState('');

  // Modales
  const [newChatModal, setNewChatModal]   = useState(false);
  const [newGroupModal, setNewGroupModal] = useState(false);
  const [userSearch, setUserSearch]       = useState('');
  const [userResults, setUserResults]     = useState([]);
  const [groupName, setGroupName]         = useState('');
  const [groupSearch, setGroupSearch]     = useState('');
  const [groupResults, setGroupResults]   = useState([]);
  const [groupSelected, setGroupSelected] = useState(new Set());

  const pollRef  = useRef(null);
  const flatRef  = useRef(null);

  // ── Cargar usuario ──────────────────────────────────────────
  useEffect(() => {
    AsyncStorage.getItem('user').then(u => {
      if (u) setUser(JSON.parse(u));
      else navigation.replace('Login');
    });
  }, []);

  useEffect(() => { if (user) loadList(); }, [user, tab]);

  // ── Lista ───────────────────────────────────────────────────
  async function loadList() {
    setLoadingList(true);
    if (tab === 'ai') { setList([]); setLoadingList(false); return; }
    const endpoint = tab === 'chats'
      ? 'api/conversations.php?action=list'
      : 'api/groups.php?action=list';
    const r = await api(endpoint);
    if (r.ok) setList(tab === 'chats' ? (r.conversations || []) : (r.groups || []));
    setLoadingList(false);
  }

  // ── Abrir chat ──────────────────────────────────────────────
  function openConv(item) {
    stopPoll();
    setMessages([]);
    setLastMsgId(0);
    setActiveChat({ type: 'conv', id: item.id, name: item.otro_nombre, otroId: item.otro_id });
  }

  function openGroup(item) {
    stopPoll();
    setMessages([]);
    setLastMsgId(0);
    setActiveChat({ type: 'group', id: item.id, name: item.nombre, miembros: item.total_miembros });
  }

  function openAI() {
    stopPoll();
    setMessages([{
      id: -1, remitente_id: 'ai', texto: '¡Hola! Soy tu asistente IA integrado en ChatTo. ¿En qué puedo ayudarte?',
      created_at: new Date().toISOString(), isAI: true
    }]);
    setAiHistory([]);
    setActiveChat({ type: 'ai', id: 0, name: 'Asistente IA' });
  }

  // ── Mensajes ────────────────────────────────────────────────
  useEffect(() => {
    if (!activeChat) return;
    if (activeChat.type !== 'ai') {
      fetchMessages(0).then(() => startPoll());
    }
    return () => stopPoll();
  }, [activeChat]);

  async function fetchMessages(sinceId) {
    if (!activeChat || activeChat.type === 'ai') return;
    const url = activeChat.type === 'conv'
      ? `api/messages.php?action=get&conv_id=${activeChat.id}&since_id=${sinceId}`
      : `api/messages.php?action=get_group&grupo_id=${activeChat.id}&since_id=${sinceId}`;
    const r = await api(url);
    if (!r.ok || !r.messages?.length) return;
    const newMsgs = r.messages;
    setMessages(prev => sinceId === 0 ? newMsgs : [...prev, ...newMsgs]);
    setLastMsgId(newMsgs[newMsgs.length - 1].id);
    setTimeout(() => flatRef.current?.scrollToEnd({ animated: true }), 100);
  }

  function startPoll() {
    stopPoll();
    pollRef.current = setInterval(() => {
      setLastMsgId(prev => { fetchMessagesSince(prev); return prev; });
    }, 2500);
  }

  function stopPoll() {
    if (pollRef.current) { clearInterval(pollRef.current); pollRef.current = null; }
  }

  async function fetchMessagesSince(sinceId) {
    if (!activeChat || activeChat.type === 'ai') return;
    const url = activeChat.type === 'conv'
      ? `api/messages.php?action=get&conv_id=${activeChat.id}&since_id=${sinceId}`
      : `api/messages.php?action=get_group&grupo_id=${activeChat.id}&since_id=${sinceId}`;
    const r = await api(url);
    if (!r.ok || !r.messages?.length) return;
    setMessages(prev => [...prev, ...r.messages]);
    setLastMsgId(r.messages[r.messages.length - 1].id);
    setTimeout(() => flatRef.current?.scrollToEnd({ animated: true }), 100);
  }

  // ── Enviar mensaje ──────────────────────────────────────────
  async function sendMessage() {
    const texto = msgInput.trim();
    if (!texto || !activeChat) return;
    setMsgInput('');

    if (activeChat.type === 'ai') {
      await sendAI(texto);
      return;
    }

    const optimistic = { id: Date.now(), remitente_id: user.id, remitente_nombre: user.nombre, texto, created_at: new Date().toISOString() };
    setMessages(prev => [...prev, optimistic]);
    setTimeout(() => flatRef.current?.scrollToEnd({ animated: true }), 100);

    const action = activeChat.type === 'conv' ? 'send' : 'send_group';
    const key    = activeChat.type === 'conv' ? 'conv_id' : 'grupo_id';
    await api(`api/messages.php?action=${action}`, 'POST', { [key]: activeChat.id, texto });
    loadList();
  }

  async function sendAI(texto) {
    const userMsg = { id: Date.now(), remitente_id: user.id, texto, created_at: new Date().toISOString() };
    const newHistory = [...aiHistory, { role: 'user', content: texto }];
    setMessages(prev => [...prev, userMsg]);
    setAiHistory(newHistory);
    setAiTyping(true);
    setTimeout(() => flatRef.current?.scrollToEnd({ animated: true }), 100);

    const r = await api('api/ai.php?action=chat', 'POST', { messages: newHistory });
    setAiTyping(false);
    const reply = r.ok ? r.reply : '⚠️ ' + (r.error || 'Error al conectar con la IA');
    setMessages(prev => [...prev, { id: Date.now() + 1, remitente_id: 'ai', texto: reply, created_at: new Date().toISOString(), isAI: true }]);
    setAiHistory(prev => [...prev, { role: 'assistant', content: reply }]);
    setTimeout(() => flatRef.current?.scrollToEnd({ animated: true }), 100);
  }

  // ── Buscar usuarios ─────────────────────────────────────────
  async function searchUsers(q) {
    setUserSearch(q);
    if (!q.trim()) { setUserResults([]); return; }
    const r = await api(`api/users.php?action=search&q=${encodeURIComponent(q)}`);
    setUserResults(r.ok ? r.users : []);
  }

  async function startConvWith(u) {
    setNewChatModal(false);
    const r = await api('api/conversations.php?action=create', 'POST', { otro_id: u.id });
    if (r.ok) { await loadList(); openConv({ id: r.id, otro_nombre: u.nombre, otro_id: u.id }); }
    else Alert.alert('Error', 'No se pudo crear la conversación');
  }

  async function searchGroupUsers(q) {
    setGroupSearch(q);
    if (!q.trim()) { setGroupResults([]); return; }
    const r = await api(`api/users.php?action=search&q=${encodeURIComponent(q)}`);
    setGroupResults(r.ok ? r.users : []);
  }

  function toggleGroupUser(id) {
    setGroupSelected(prev => {
      const s = new Set(prev);
      s.has(id) ? s.delete(id) : s.add(id);
      return s;
    });
  }

  async function createGroup() {
    if (!groupName.trim()) { Alert.alert('Error', 'Escribe el nombre del grupo'); return; }
    const r = await api('api/groups.php?action=create', 'POST', { nombre: groupName.trim(), miembros: [...groupSelected] });
    if (r.ok) {
      setNewGroupModal(false);
      setGroupName(''); setGroupSearch(''); setGroupResults([]); setGroupSelected(new Set());
      await loadList();
      openGroup({ id: r.id, nombre: r.nombre, total_miembros: groupSelected.size + 1 });
    } else Alert.alert('Error', 'No se pudo crear el grupo');
  }

  function logout() {
    Alert.alert('Cerrar sesión', '¿Estás seguro?', [
      { text: 'Cancelar' },
      { text: 'Salir', style: 'destructive', onPress: async () => {
        await api('api/auth.php?action=logout');
        await AsyncStorage.removeItem('user');
        navigation.replace('Login');
      }}
    ]);
  }

  // ── Filtrar lista ───────────────────────────────────────────
  const filteredList = list.filter(item => {
    const name = tab === 'chats' ? item.otro_nombre : item.nombre;
    return name?.toLowerCase().includes(search.toLowerCase());
  });

  // ── Render mensaje ──────────────────────────────────────────
  function renderMessage({ item }) {
    const isMe = item.remitente_id === user?.id;
    const isAI = item.isAI || item.remitente_id === 'ai';
    const time = item.created_at
      ? new Date(item.created_at).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })
      : '';

    return (
      <View style={[styles.msgWrap, isMe ? styles.msgOut : styles.msgIn]}>
        {!isMe && activeChat?.type === 'group' && item.remitente_nombre && (
          <Text style={styles.msgSender}>{item.remitente_nombre}</Text>
        )}
        {isAI && <Text style={styles.aiSender}>🤖 Asistente IA</Text>}
        <View style={[styles.bubble, isMe ? styles.bubbleOut : styles.bubbleIn, isAI && styles.bubbleAI]}>
          <Text style={styles.bubbleTxt}>{item.texto}</Text>
        </View>
        <Text style={styles.msgTime}>{time}</Text>
      </View>
    );
  }

  // ── Si hay chat activo, mostrar pantalla de chat ────────────
  if (activeChat) {
    return (
      <SafeAreaView style={styles.root}>
        {/* Header del chat */}
        <View style={styles.chatHeader}>
          <TouchableOpacity onPress={() => { stopPoll(); setActiveChat(null); setMessages([]); }} style={styles.backBtn}>
            <Text style={styles.backTxt}>←</Text>
          </TouchableOpacity>
          <Avatar name={activeChat.name} type={activeChat.type === 'conv' ? 'personal' : activeChat.type} size={40} />
          <View style={styles.chatHeaderInfo}>
            <Text style={styles.chatHeaderName}>{activeChat.name}</Text>
            <Text style={styles.chatHeaderSub}>
              {activeChat.type === 'conv'  ? 'Chat privado' :
               activeChat.type === 'group' ? `${activeChat.miembros} miembros` :
               'Claude · Responde al instante'}
            </Text>
          </View>
        </View>

        {/* Mensajes */}
        <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : undefined} keyboardVerticalOffset={0}>
          <FlatList
            ref={flatRef}
            data={messages}
            keyExtractor={m => String(m.id)}
            renderItem={renderMessage}
            contentContainerStyle={styles.msgList}
            onContentSizeChange={() => flatRef.current?.scrollToEnd({ animated: false })}
            ListEmptyComponent={<Text style={styles.emptyMsgs}>Sin mensajes aún. ¡Di hola! 👋</Text>}
          />
          {aiTyping && (
            <View style={styles.typingWrap}>
              <View style={styles.typingBubble}>
                <Text style={styles.typingTxt}>🤖 escribiendo...</Text>
              </View>
            </View>
          )}

          {/* Input */}
          <View style={styles.inputArea}>
            <TextInput
              style={styles.msgInputField}
              placeholder="Escribe un mensaje..."
              placeholderTextColor="#475569"
              value={msgInput}
              onChangeText={setMsgInput}
              multiline
              maxLength={2000}
            />
            <TouchableOpacity style={styles.sendBtn} onPress={sendMessage}>
              <Text style={styles.sendTxt}>➤</Text>
            </TouchableOpacity>
          </View>
        </KeyboardAvoidingView>
      </SafeAreaView>
    );
  }

  // ── Pantalla principal ──────────────────────────────────────
  return (
    <SafeAreaView style={styles.root}>

      {/* Header */}
      <View style={styles.header}>
        <View style={styles.headerLeft}>
          <View style={styles.headerAvatar}>
            <Text style={styles.headerAvatarTxt}>{initials(user?.nombre)}</Text>
          </View>
          <View>
            <Text style={styles.headerName}>{user?.nombre}</Text>
            <Text style={styles.headerStatus}>● En línea</Text>
          </View>
        </View>
        <View style={styles.headerActions}>
          <TouchableOpacity style={styles.iconBtn} onPress={() => { setUserSearch(''); setUserResults([]); setNewChatModal(true); }}>
            <Text style={styles.iconBtnTxt}>✏️</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.iconBtn} onPress={() => { setGroupName(''); setGroupSearch(''); setGroupResults([]); setGroupSelected(new Set()); setNewGroupModal(true); }}>
            <Text style={styles.iconBtnTxt}>👥</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.iconBtn} onPress={logout}>
            <Text style={styles.iconBtnTxt}>🚪</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* Buscador */}
      <View style={styles.searchWrap}>
        <TextInput
          style={styles.searchInput}
          placeholder="🔍  Buscar conversación..."
          placeholderTextColor="#475569"
          value={search}
          onChangeText={setSearch}
        />
      </View>

      {/* Tabs */}
      <View style={styles.tabsBar}>
        {['chats','groups','ai'].map(t => (
          <TouchableOpacity key={t} style={[styles.tabBtn, tab === t && styles.tabActive]} onPress={() => setTab(t)}>
            <Text style={[styles.tabTxt, tab === t && styles.tabTxtActive]}>
              {t === 'chats' ? '💬 Chats' : t === 'groups' ? '👥 Grupos' : '🤖 IA'}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Lista */}
      {tab === 'ai' ? (
        <TouchableOpacity style={styles.aiItem} onPress={openAI}>
          <Avatar name="AI" type="ai" size={46} />
          <View style={styles.ciInfo}>
            <Text style={styles.ciName}>Asistente IA</Text>
            <Text style={styles.ciPreview}>Claude · Chat inteligente</Text>
          </View>
        </TouchableOpacity>
      ) : loadingList ? (
        <ActivityIndicator color="#6366f1" style={{ marginTop: 40 }} />
      ) : filteredList.length === 0 ? (
        <View style={styles.emptyList}>
          <Text style={styles.emptyIcon}>{tab === 'chats' ? '💬' : '👥'}</Text>
          <Text style={styles.emptyTxt}>
            {tab === 'chats' ? 'Sin conversaciones aún\nToca ✏️ para iniciar un chat' : 'Sin grupos aún\nToca 👥 para crear uno'}
          </Text>
        </View>
      ) : (
        <FlatList
          data={filteredList}
          keyExtractor={i => String(i.id)}
          renderItem={({ item }) => (
            <TouchableOpacity
              style={styles.chatItem}
              onPress={() => tab === 'chats' ? openConv(item) : openGroup(item)}
            >
              <Avatar name={tab === 'chats' ? item.otro_nombre : item.nombre} type={tab === 'chats' ? 'personal' : 'group'} size={46} />
              <View style={styles.ciInfo}>
                <Text style={styles.ciName}>{tab === 'chats' ? item.otro_nombre : item.nombre}</Text>
                <Text style={styles.ciPreview} numberOfLines={1}>
                  {item.preview || (tab === 'groups' ? `${item.total_miembros} miembros` : 'Sin mensajes')}
                </Text>
              </View>
              <Text style={styles.ciTime}>{fmtTime(item.ultimo_at)}</Text>
            </TouchableOpacity>
          )}
        />
      )}

      {/* Modal: nueva conversación */}
      <Modal visible={newChatModal} transparent animationType="slide" onRequestClose={() => setNewChatModal(false)}>
        <View style={styles.modalOverlay}>
          <View style={styles.modal}>
            <Text style={styles.modalTitle}>Nueva conversación</Text>
            <TextInput
              style={styles.modalInput}
              placeholder="Buscar usuario..."
              placeholderTextColor="#475569"
              value={userSearch}
              onChangeText={searchUsers}
            />
            <ScrollView style={styles.modalScroll}>
              {userResults.length === 0
                ? <Text style={styles.modalEmpty}>Escribe un nombre o correo</Text>
                : userResults.map(u => (
                  <TouchableOpacity key={u.id} style={styles.userItem} onPress={() => startConvWith(u)}>
                    <Avatar name={u.nombre} size={38} />
                    <View style={{ marginLeft: 10 }}>
                      <Text style={styles.uName}>{u.nombre}</Text>
                      <Text style={styles.uEmail}>{u.email}</Text>
                    </View>
                  </TouchableOpacity>
                ))
              }
            </ScrollView>
            <TouchableOpacity style={styles.btnCancel} onPress={() => setNewChatModal(false)}>
              <Text style={styles.btnCancelTxt}>Cancelar</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      {/* Modal: nuevo grupo */}
      <Modal visible={newGroupModal} transparent animationType="slide" onRequestClose={() => setNewGroupModal(false)}>
        <View style={styles.modalOverlay}>
          <View style={styles.modal}>
            <Text style={styles.modalTitle}>Crear grupo</Text>
            <TextInput
              style={styles.modalInput}
              placeholder="Nombre del grupo"
              placeholderTextColor="#475569"
              value={groupName}
              onChangeText={setGroupName}
            />
            <TextInput
              style={styles.modalInput}
              placeholder="Agregar miembros..."
              placeholderTextColor="#475569"
              value={groupSearch}
              onChangeText={searchGroupUsers}
            />
            <ScrollView style={styles.modalScroll}>
              {groupResults.map(u => (
                <TouchableOpacity key={u.id} style={styles.userItem} onPress={() => toggleGroupUser(u.id)}>
                  <View style={[styles.checkbox, groupSelected.has(u.id) && styles.checkboxActive]}>
                    {groupSelected.has(u.id) && <Text style={{ color: '#fff', fontSize: 12 }}>✓</Text>}
                  </View>
                  <Avatar name={u.nombre} size={36} />
                  <View style={{ marginLeft: 10 }}>
                    <Text style={styles.uName}>{u.nombre}</Text>
                    <Text style={styles.uEmail}>{u.email}</Text>
                  </View>
                </TouchableOpacity>
              ))}
            </ScrollView>
            <View style={styles.modalFooter}>
              <TouchableOpacity style={styles.btnCancel} onPress={() => setNewGroupModal(false)}>
                <Text style={styles.btnCancelTxt}>Cancelar</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.btnConfirm} onPress={createGroup}>
                <Text style={styles.btnConfirmTxt}>Crear grupo</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  root:             { flex: 1, backgroundColor: '#07090f' },

  // Header
  header:           { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', padding: 16, borderBottomWidth: 1, borderBottomColor: '#1e2545', backgroundColor: '#0d1021' },
  headerLeft:       { flexDirection: 'row', alignItems: 'center', gap: 10 },
  headerAvatar:     { width: 42, height: 42, borderRadius: 21, backgroundColor: '#6366f1', alignItems: 'center', justifyContent: 'center' },
  headerAvatarTxt:  { color: '#fff', fontWeight: '700', fontSize: 16 },
  headerName:       { color: '#f1f5f9', fontWeight: '600', fontSize: 15 },
  headerStatus:     { color: '#10b981', fontSize: 12 },
  headerActions:    { flexDirection: 'row', gap: 4 },
  iconBtn:          { width: 36, height: 36, borderRadius: 10, alignItems: 'center', justifyContent: 'center' },
  iconBtnTxt:       { fontSize: 18 },

  // Search
  searchWrap:       { padding: 10, borderBottomWidth: 1, borderBottomColor: '#1e2545' },
  searchInput:      { backgroundColor: '#111428', borderWidth: 1, borderColor: '#1e2545', borderRadius: 10, padding: 10, color: '#f1f5f9', fontSize: 14 },

  // Tabs
  tabsBar:          { flexDirection: 'row', padding: 8, gap: 6, borderBottomWidth: 1, borderBottomColor: '#1e2545' },
  tabBtn:           { flex: 1, paddingVertical: 8, borderRadius: 8, alignItems: 'center' },
  tabActive:        { backgroundColor: 'rgba(99,102,241,.15)' },
  tabTxt:           { color: '#64748b', fontSize: 13, fontWeight: '500' },
  tabTxtActive:     { color: '#6366f1', fontWeight: '700' },

  // Lista
  chatItem:         { flexDirection: 'row', alignItems: 'center', padding: 14, borderBottomWidth: 1, borderBottomColor: 'rgba(255,255,255,.03)' },
  aiItem:           { flexDirection: 'row', alignItems: 'center', padding: 14 },
  ciInfo:           { flex: 1, marginLeft: 12 },
  ciName:           { color: '#f1f5f9', fontWeight: '600', fontSize: 14 },
  ciPreview:        { color: '#64748b', fontSize: 12, marginTop: 2 },
  ciTime:           { color: '#64748b', fontSize: 11, marginLeft: 6 },
  emptyList:        { alignItems: 'center', padding: 40 },
  emptyIcon:        { fontSize: 40, marginBottom: 12 },
  emptyTxt:         { color: '#64748b', fontSize: 14, textAlign: 'center', lineHeight: 22 },

  // Avatar
  avatar:           { alignItems: 'center', justifyContent: 'center' },
  avatarTxt:        { color: '#fff', fontWeight: '700' },

  // Chat header
  chatHeader:       { flexDirection: 'row', alignItems: 'center', padding: 14, backgroundColor: '#0d1021', borderBottomWidth: 1, borderBottomColor: '#1e2545', gap: 10 },
  backBtn:          { width: 36, height: 36, alignItems: 'center', justifyContent: 'center' },
  backTxt:          { color: '#f1f5f9', fontSize: 22 },
  chatHeaderInfo:   { flex: 1 },
  chatHeaderName:   { color: '#f1f5f9', fontWeight: '600', fontSize: 15 },
  chatHeaderSub:    { color: '#64748b', fontSize: 12 },

  // Mensajes
  msgList:          { padding: 16, paddingBottom: 8 },
  msgWrap:          { marginBottom: 8, maxWidth: '75%' },
  msgOut:           { alignSelf: 'flex-end', alignItems: 'flex-end' },
  msgIn:            { alignSelf: 'flex-start', alignItems: 'flex-start' },
  bubble:           { padding: 10, borderRadius: 14 },
  bubbleOut:        { backgroundColor: '#5b21b6', borderBottomRightRadius: 4 },
  bubbleIn:         { backgroundColor: '#1a1f3a', borderBottomLeftRadius: 4, borderWidth: 1, borderColor: '#1e2545' },
  bubbleAI:         { backgroundColor: 'rgba(124,39,18,.4)', borderWidth: 1, borderColor: 'rgba(245,158,11,.2)' },
  bubbleTxt:        { color: '#f1f5f9', fontSize: 14, lineHeight: 20 },
  msgSender:        { color: '#6366f1', fontSize: 11, fontWeight: '600', marginBottom: 3 },
  aiSender:         { color: '#f59e0b', fontSize: 11, fontWeight: '600', marginBottom: 3 },
  msgTime:          { color: '#64748b', fontSize: 11, marginTop: 4 },
  emptyMsgs:        { textAlign: 'center', color: '#64748b', fontSize: 14, marginTop: 60 },

  // Typing
  typingWrap:       { paddingHorizontal: 16, paddingBottom: 4 },
  typingBubble:     { backgroundColor: '#1a1f3a', borderRadius: 14, padding: 10, alignSelf: 'flex-start' },
  typingTxt:        { color: '#64748b', fontSize: 13 },

  // Input
  inputArea:        { flexDirection: 'row', alignItems: 'flex-end', padding: 12, backgroundColor: '#0d1021', borderTopWidth: 1, borderTopColor: '#1e2545', gap: 8 },
  msgInputField:    { flex: 1, backgroundColor: '#111428', borderWidth: 1, borderColor: '#1e2545', borderRadius: 14, padding: 12, color: '#f1f5f9', fontSize: 15, maxHeight: 100 },
  sendBtn:          { width: 46, height: 46, borderRadius: 14, backgroundColor: '#6366f1', alignItems: 'center', justifyContent: 'center' },
  sendTxt:          { color: '#fff', fontSize: 18 },

  // Modales
  modalOverlay:     { flex: 1, backgroundColor: 'rgba(0,0,0,.8)', justifyContent: 'flex-end' },
  modal:            { backgroundColor: '#141829', borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: 24, maxHeight: '80%' },
  modalTitle:       { color: '#f1f5f9', fontSize: 18, fontWeight: '700', marginBottom: 16 },
  modalInput:       { backgroundColor: '#0e1120', borderWidth: 1, borderColor: '#1e2545', borderRadius: 10, padding: 12, color: '#f1f5f9', fontSize: 14, marginBottom: 12 },
  modalScroll:      { maxHeight: 280, marginBottom: 12 },
  modalEmpty:       { color: '#64748b', fontSize: 13, textAlign: 'center', padding: 20 },
  modalFooter:      { flexDirection: 'row', gap: 10 },
  userItem:         { flexDirection: 'row', alignItems: 'center', padding: 10, borderRadius: 10 },
  uName:            { color: '#f1f5f9', fontSize: 14, fontWeight: '500' },
  uEmail:           { color: '#64748b', fontSize: 12 },
  checkbox:         { width: 20, height: 20, borderRadius: 6, borderWidth: 2, borderColor: '#1e2545', marginRight: 10, alignItems: 'center', justifyContent: 'center' },
  checkboxActive:   { backgroundColor: '#6366f1', borderColor: '#6366f1' },
  btnCancel:        { flex: 1, padding: 13, borderRadius: 10, backgroundColor: '#1e2545', alignItems: 'center' },
  btnCancelTxt:     { color: '#64748b', fontWeight: '600' },
  btnConfirm:       { flex: 1, padding: 13, borderRadius: 10, backgroundColor: '#6366f1', alignItems: 'center' },
  btnConfirmTxt:    { color: '#fff', fontWeight: '600' },
});