import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  StyleSheet, Alert, ActivityIndicator,
  KeyboardAvoidingView, Platform, ScrollView
} from 'react-native';
import * as LocalAuthentication from 'expo-local-authentication';
import AsyncStorage from '@react-native-async-storage/async-storage';

const BASE = 'https://limegreen-elk-680782.hostingersite.com';

export default function LoginScreen({ navigation }) {
  const [email, setEmail]       = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading]   = useState(false);
  const [tab, setTab]           = useState('password'); // 'password' | 'finger'

  // ── Login con contraseña ──────────────────────────────────
  async function handleLogin() {
    if (!email.trim() || !password.trim()) {
      Alert.alert('Error', 'Ingresa tu correo y contraseña');
      return;
    }
    setLoading(true);
    try {
      const res  = await fetch(`${BASE}/api/auth.php?action=login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email.trim(), password }),
      });
      const data = await res.json();
      if (data.ok) {
        await AsyncStorage.setItem('user', JSON.stringify({ id: data.id, nombre: data.nombre, email: email.trim() }));
        navigation.replace('App');
      } else {
        Alert.alert('Error', data.error || 'Credenciales incorrectas');
      }
    } catch {
      Alert.alert('Error', 'No se pudo conectar al servidor');
    }
    setLoading(false);
  }

  // ── Login con huella ──────────────────────────────────────
  async function handleFingerprint() {
    try {
      // 1. Verificar si el dispositivo soporta biometría
      const compatible = await LocalAuthentication.hasHardwareAsync();
      if (!compatible) {
        Alert.alert('No disponible', 'Tu dispositivo no tiene sensor de huella dactilar');
        return;
      }

      // 2. Verificar si hay huellas registradas
      const enrolled = await LocalAuthentication.isEnrolledAsync();
      if (!enrolled) {
        Alert.alert('Sin huella', 'No tienes ninguna huella registrada en este dispositivo. Ve a Ajustes > Seguridad para registrarla.');
        return;
      }

      // 3. Pedir autenticación biométrica
      const result = await LocalAuthentication.authenticateAsync({
        promptMessage: 'Usa tu huella para entrar a ChatTo',
        cancelLabel: 'Cancelar',
        fallbackLabel: 'Usar contraseña',
        disableDeviceFallback: false,
      });

      if (result.success) {
        // 4. Recuperar usuario guardado localmente
        const saved = await AsyncStorage.getItem('user');
        if (!saved) {
          Alert.alert(
            'Primera vez',
            'Inicia sesión con tu contraseña primero. Después podrás usar tu huella.'
          );
          return;
        }
        // 5. Ir a la app directamente
        navigation.replace('App');
      }
    } catch (e) {
      Alert.alert('Error', 'No se pudo verificar la huella');
    }
  }

  return (
    <KeyboardAvoidingView
      style={styles.root}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">

        {/* Logo */}
        <View style={styles.logoWrap}>
          <View style={styles.logoIcon}><Text style={styles.logoEmoji}>💬</Text></View>
          <Text style={styles.logoText}>ChatTo</Text>
        </View>

        <Text style={styles.title}>Bienvenido de vuelta</Text>
        <Text style={styles.subtitle}>Inicia sesión para continuar</Text>

        {/* Tabs */}
        <View style={styles.tabs}>
          <TouchableOpacity
            style={[styles.tabBtn, tab === 'password' && styles.tabActive]}
            onPress={() => setTab('password')}
          >
            <Text style={[styles.tabTxt, tab === 'password' && styles.tabTxtActive]}>🔑 Contraseña</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tabBtn, tab === 'finger' && styles.tabActive]}
            onPress={() => setTab('finger')}
          >
            <Text style={[styles.tabTxt, tab === 'finger' && styles.tabTxtActive]}>👆 Huella</Text>
          </TouchableOpacity>
        </View>

        {/* Tab: contraseña */}
        {tab === 'password' && (
          <View style={styles.form}>
            <Text style={styles.label}>Correo electrónico</Text>
            <TextInput
              style={styles.input}
              placeholder="tu@correo.com"
              placeholderTextColor="#475569"
              keyboardType="email-address"
              autoCapitalize="none"
              value={email}
              onChangeText={setEmail}
            />
            <Text style={styles.label}>Contraseña</Text>
            <TextInput
              style={styles.input}
              placeholder="Tu contraseña"
              placeholderTextColor="#475569"
              secureTextEntry
              value={password}
              onChangeText={setPassword}
            />
            <TouchableOpacity style={styles.btnPrimary} onPress={handleLogin} disabled={loading}>
              {loading
                ? <ActivityIndicator color="#fff" />
                : <Text style={styles.btnTxt}>Iniciar sesión</Text>
              }
            </TouchableOpacity>
          </View>
        )}

        {/* Tab: huella */}
        {tab === 'finger' && (
          <View style={styles.fingerWrap}>
            <TouchableOpacity style={styles.fingerBtn} onPress={handleFingerprint}>
              <Text style={styles.fingerIcon}>👆</Text>
            </TouchableOpacity>
            <Text style={styles.fingerHint}>Toca para escanear tu huella dactilar</Text>
          </View>
        )}

        {/* Link registro */}
        <TouchableOpacity onPress={() => navigation.navigate('Register')}>
          <Text style={styles.registerLink}>
            ¿No tienes cuenta? <Text style={styles.registerLinkBold}>Regístrate aquí</Text>
          </Text>
        </TouchableOpacity>

      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  root:        { flex: 1, backgroundColor: '#07090f' },
  scroll:      { flexGrow: 1, alignItems: 'center', justifyContent: 'center', padding: 28 },

  logoWrap:    { flexDirection: 'row', alignItems: 'center', gap: 10, marginBottom: 28 },
  logoIcon:    { width: 48, height: 48, borderRadius: 14, backgroundColor: '#6366f1', alignItems: 'center', justifyContent: 'center' },
  logoEmoji:   { fontSize: 24 },
  logoText:    { fontFamily: 'System', fontSize: 30, fontWeight: '700', color: '#f1f5f9' },

  title:       { fontSize: 22, fontWeight: '700', color: '#f1f5f9', marginBottom: 6 },
  subtitle:    { fontSize: 14, color: '#64748b', marginBottom: 28 },

  tabs:        { flexDirection: 'row', backgroundColor: '#0e1120', borderRadius: 12, padding: 4, marginBottom: 28, width: '100%' },
  tabBtn:      { flex: 1, paddingVertical: 10, borderRadius: 10, alignItems: 'center' },
  tabActive:   { backgroundColor: '#1e2545' },
  tabTxt:      { fontSize: 14, color: '#64748b', fontWeight: '500' },
  tabTxtActive:{ color: '#6366f1', fontWeight: '700' },

  form:        { width: '100%' },
  label:       { fontSize: 13, color: '#64748b', fontWeight: '500', marginBottom: 7 },
  input:       { width: '100%', backgroundColor: '#0e1120', borderWidth: 1, borderColor: '#1e2545', borderRadius: 12, padding: 14, color: '#f1f5f9', fontSize: 15, marginBottom: 16 },

  btnPrimary:  { backgroundColor: '#6366f1', borderRadius: 12, padding: 16, alignItems: 'center', marginTop: 4 },
  btnTxt:      { color: '#fff', fontSize: 15, fontWeight: '700' },

  fingerWrap:  { alignItems: 'center', paddingVertical: 20, width: '100%' },
  fingerBtn:   { width: 120, height: 120, borderRadius: 60, backgroundColor: '#1e2545', borderWidth: 2, borderColor: '#6366f1', alignItems: 'center', justifyContent: 'center', marginBottom: 20 },
  fingerIcon:  { fontSize: 56 },
  fingerHint:  { fontSize: 14, color: '#64748b', textAlign: 'center' },

  registerLink:     { marginTop: 28, fontSize: 13, color: '#64748b', textAlign: 'center' },
  registerLinkBold: { color: '#6366f1', fontWeight: '600' },
});