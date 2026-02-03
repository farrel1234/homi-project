package com.example.homi.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import com.example.homi.data.repository.AccountRepository
import kotlinx.coroutines.launch

@Composable
fun UbahKataSandiScreen(
    accountRepo: AccountRepository,
    onBack: () -> Unit
) {
    val scope = rememberCoroutineScope()
    val snackbar = remember { SnackbarHostState() }

    var currentPassword by remember { mutableStateOf("") }
    var newPassword by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }
    var loading by remember { mutableStateOf(false) }

    Scaffold(
        snackbarHost = { SnackbarHost(snackbar) }
    ) { pad ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(pad)
                .padding(16.dp)
        ) {
            Text("Ubah Kata Sandi", style = MaterialTheme.typography.titleLarge)
            Spacer(Modifier.height(4.dp))
            TextButton(onClick = onBack) { Text("Kembali") }

            Spacer(Modifier.height(12.dp))

            OutlinedTextField(
                value = currentPassword,
                onValueChange = { currentPassword = it },
                label = { Text("Kata sandi saat ini") },
                visualTransformation = PasswordVisualTransformation(),
                singleLine = true,
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(Modifier.height(12.dp))

            OutlinedTextField(
                value = newPassword,
                onValueChange = { newPassword = it },
                label = { Text("Kata sandi baru") },
                visualTransformation = PasswordVisualTransformation(),
                singleLine = true,
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(Modifier.height(12.dp))

            OutlinedTextField(
                value = confirmPassword,
                onValueChange = { confirmPassword = it },
                label = { Text("Konfirmasi kata sandi baru") },
                visualTransformation = PasswordVisualTransformation(),
                singleLine = true,
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(Modifier.height(20.dp))

            Button(
                onClick = {
                    if (currentPassword.isBlank() || newPassword.isBlank() || confirmPassword.isBlank()) {
                        scope.launch { snackbar.showSnackbar("Semua field wajib diisi.") }
                        return@Button
                    }
                    if (newPassword.length < 6) {
                        scope.launch { snackbar.showSnackbar("Password baru minimal 6 karakter.") }
                        return@Button
                    }
                    if (newPassword != confirmPassword) {
                        scope.launch { snackbar.showSnackbar("Konfirmasi password tidak sama.") }
                        return@Button
                    }
                    if (loading) return@Button

                    loading = true
                    scope.launch {
                        try {
                            val res = accountRepo.changePassword(
                                currentPassword = currentPassword,
                                newPassword = newPassword,
                                newPasswordConfirmation = confirmPassword
                            )

                            loading = false
                            val ok = res.success == true   // handle kalau success Boolean?
                            val msg = res.message?.takeIf { it.isNotBlank() }

                            if (ok) {
                                snackbar.showSnackbar(msg ?: "Password berhasil diubah.")
                                onBack()
                            } else {
                                snackbar.showSnackbar(msg ?: "Gagal mengubah password.")
                            }
                        } catch (e: Exception) {
                            loading = false
                            snackbar.showSnackbar(e.message ?: "Terjadi kesalahan.")
                        }
                    }
                },
                enabled = !loading,
                modifier = Modifier.fillMaxWidth()
            ) {
                if (loading) {
                    CircularProgressIndicator(
                        modifier = Modifier.size(18.dp),
                        strokeWidth = 2.dp
                    )
                    Spacer(Modifier.width(10.dp))
                }
                Text("Simpan")
            }

            Spacer(Modifier.height(12.dp))

            TextButton(
                onClick = onBack,
                modifier = Modifier.align(Alignment.End)
            ) {
                Text("Batal")
            }
        }
    }
}
