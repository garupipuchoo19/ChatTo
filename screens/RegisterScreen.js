import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  StyleSheet, Alert, ActivityIndicator,
  KeyboardAvoidingView, Platform, ScrollView
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const BASE = 'https://limegreen-elk-680782.hostingersite.com';

export default function RegisterScreen({ navigation }) {
  const [nombre, setNombre]     = useState('');
  const [email, setEmail]       = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading]   = useState(false);

  async function handleRegister() {
    if (!nombre.trim() || !email.trim() || !password.trim()) {
      Alert.alert('Error', 'Todos los campos son requeridos');
      return;
    }
    if (password.length < 6) {
      Alert.alert('Error', 'La contraseña debe tener mínimo 6 caracteres');
      return;
    }
    setLoading(true);
    try {
      const res  = await fetch(`${BASE}/api/auth.php?action=register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre: nombre.trim(), email: email.trim(), password }),
      });
      const data = await res.json();
      if (data.ok) {
        await AsyncStorage.setItem('user', JSON.stringify({
          id: data.id, nombre: data.nombre, email: email.trim()
        }));
        navigation.replace('App');
      } else {
        Alert.alert('Error', data.error || 'No se pudo registrar');
      }
    } catch {
      Alert.alert('Error', 'No se pudo conectar al servidor');
    }
    setLoading(false);
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

        <Text style={styles.title}>Crear cuenta</Text>
        <Text style={styles.subtitle}>Únete a ChatTo y empieza a chatear</Text>

        <View style={styles.form}>
          <Text style={styles.label}>Nombre completo</Text>
          <TextInput
            style={styles.input}
            placeholder="Tu nombre"
            placeholderTextColor="#475569"
            value={nombre}
            onChangeText={setNombre}
          />

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
            placeholder="Mínimo 6 caracteres"
            placeholderTextColor="#475569"
            secureTextEntry
            value={password}
            onChangeText={setPassword}
          />

          <View style={styles.infoBox}>
            <Text style={styles.infoText}>👆 Podrás activar el login con huella después de registrarte, iniciando sesión desde la pantalla principal.</Text>
          </View>

          <TouchableOpacity style={styles.btnPrimary} onPress={handleRegister} disabled={loading}>
            {loading
              ? <ActivityIndicator color="#fff" />
              : <Text style={styles.btnTxt}>Crear cuenta</Text>
            }
          </TouchableOpacity>
        </View>

        <TouchableOpacity onPress={() => navigation.navigate('Login')}>
          <Text style={styles.loginLink}>
            ¿Ya tienes cuenta? <Text style={styles.loginLinkBold}>Iniciar sesión</Text>
          </Text>
        </TouchableOpacity>

      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  root:          { flex: 1, backgroundColor: '#07090f' },
  scroll:        { flexGrow: 1, alignItems: 'center', justifyContent: 'center', padding: 28 },

  logoWrap:      { flexDirection: 'row', alignItems: 'center', gap: 10, marginBottom: 28 },
  logoIcon:      { width: 48, height: 48, borderRadius: 14, backgroundColor: '#6366f1', alignItems: 'center', justifyContent: 'center' },
  logoEmoji:     { fontSize: 24 },
  logoText:      { fontSize: 30, fontWeight: '700', color: '#f1f5f9' },

  title:         { fontSize: 22, fontWeight: '700', color: '#f1f5f9', marginBottom: 6 },
  subtitle:      { fontSize: 14, color: '#64748b', marginBottom: 28 },

  form:          { width: '100%' },
  label:         { fontSize: 13, color: '#64748b', fontWeight: '500', marginBottom: 7 },
  input:         { width: '100%', backgroundColor: '#0e1120', borderWidth: 1, borderColor: '#1e2545', borderRadius: 12, padding: 14, color: '#f1f5f9', fontSize: 15, marginBottom: 16 },

  infoBox:       { backgroundColor: '#1e2545', borderRadius: 12, padding: 14, marginBottom: 16 },
  infoText:      { color: '#94a3b8', fontSize: 13, lineHeight: 20 },

  btnPrimary:    { backgroundColor: '#6366f1', borderRadius: 12, padding: 16, alignItems: 'center', marginTop: 4 },
  btnTxt:        { color: '#fff', fontSize: 15, fontWeight: '700' },

  loginLink:     { marginTop: 28, fontSize: 13, color: '#64748b', textAlign: 'center' },
  loginLinkBold: { color: '#6366f1', fontWeight: '600' },
});