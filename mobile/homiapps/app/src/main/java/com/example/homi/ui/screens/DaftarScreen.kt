package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.focus.FocusDirection
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.local.TokenStore
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.AuthRepository
import kotlinx.coroutines.launch
import retrofit2.HttpException

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DaftarScreen(
    tokenStore: TokenStore,
    onGoLogin: () -> Unit,
    onGoOtp: (
        email: String,
        job: String,
        houseType: String,
        housing: String,
        blok: String,
        houseNumber: String
    ) -> Unit,
    preName: String = "",
    preEmail: String = "",
    googleId: String = ""
) {
    // Theme HOMI
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val blue = Color(0xFF2F7FA3) // BlueMain
    val orange = Color(0xFFFFA06B)
    val bg = Color(0xFF2F7FA3) // Background to match the Topper

    // Form state
    var fullName by remember { mutableStateOf(preName) }
    var email by remember { mutableStateOf(preEmail) }

    // Minimal profile info for registration
    var tenantCode by remember { mutableStateOf("") } // Kode tenant rahasia

    // Password
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }

    var loading by remember { mutableStateOf(false) }

    val snackbar = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()
    val focus = LocalFocusManager.current
    val scroll = rememberScrollState()

    val api = remember { ApiClient.getApi(tokenStore) }
    val authRepo = remember { AuthRepository(api) }

    // Validasi
    val isEmailValid = remember(email) { email.trim().contains("@") && email.trim().contains(".") }
    val isPassValid = remember(password) { password.length >= 6 }
    val isFormValid =
        fullName.isNotBlank() &&
                isEmailValid &&
                isPassValid &&
                password == confirmPassword &&
                tenantCode.isNotBlank()

    val tfColors = OutlinedTextFieldDefaults.colors(
        focusedContainerColor = Color(0xFFFBFBFB),
        unfocusedContainerColor = Color(0xFFFBFBFB),
        focusedBorderColor = blue,
        unfocusedBorderColor = Color.LightGray,
        cursorColor = blue
    )

    fun extractApiMessage(raw: String?): String? {
        if (raw.isNullOrBlank()) return null
        val regex = Regex("\"message\"\\s*:\\s*\"(.*?)\"")
        val match = regex.find(raw) ?: return null
        return match.groupValues.getOrNull(1)
            ?.replace("\\n", "\n")
            ?.replace("\\\"", "\"")
    }

    suspend fun showError(e: Throwable) {
        val msg = when (e) {
            is HttpException -> {
                val body = runCatching { e.response()?.errorBody()?.string() }.getOrNull()
                extractApiMessage(body) ?: e.message()
            }
            else -> e.message ?: "Terjadi kesalahan."
        }
        snackbar.showSnackbar(msg)
    }

    Scaffold(
        snackbarHost = { SnackbarHost(snackbar) },
        containerColor = bg
    ) { pad ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(pad)
                .statusBarsPadding()
        ) {
            // TOPPER THEME
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                IconButton(onClick = onGoLogin) {
                    Image(
                        painter = painterResource(id = R.drawable.panahkembali),
                        contentDescription = "Kembali",
                        modifier = Modifier.size(24.dp)
                    )
                }

                Text(
                    text = "Daftar Warga Baru",
                    fontFamily = poppins,
                    fontSize = 22.sp,
                    color = Color.White,
                    modifier = Modifier.weight(1f),
                    textAlign = TextAlign.Center
                )
                Spacer(Modifier.width(40.dp))
            }

            Text(
                text = "Silakan lengkapi data diri Anda untuk bergabung bersama komunitas Hawaii Garden.",
                fontFamily = FontFamily(Font(R.font.poppins_regular)),
                fontSize = 12.sp,
                color = Color.White,
                textAlign = TextAlign.Center,
                lineHeight = 18.sp,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 24.dp)
            )

            Spacer(Modifier.height(16.dp))

            Card(
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp),
                modifier = Modifier.fillMaxSize()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .imePadding()
                        .verticalScroll(scroll)
                        .padding(start = 24.dp, end = 24.dp, top = 24.dp)
                ) {
                        Text(
                            "Data Akun",
                            fontFamily = poppins,
                            fontSize = 16.sp,
                            color = orange,
                            modifier = Modifier.padding(bottom = 16.dp)
                        )

                        // Nama
                        OutlinedTextField(
                            value = fullName,
                            onValueChange = { fullName = it },
                            label = { Text("Nama Lengkap") },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            keyboardOptions = KeyboardOptions(imeAction = ImeAction.Next),
                            keyboardActions = KeyboardActions(onNext = { focus.moveFocus(FocusDirection.Down) })
                        )

                        Spacer(Modifier.height(16.dp))

                        // Email
                        OutlinedTextField(
                            value = email,
                            onValueChange = { email = it },
                            label = { Text("Email Aktif") },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email, imeAction = ImeAction.Next),
                            keyboardActions = KeyboardActions(onNext = { focus.moveFocus(FocusDirection.Down) }),
                            supportingText = { if (email.isNotBlank() && !isEmailValid) Text("Format email tidak valid", color = Color.Red, fontSize = 11.sp) }
                        )

                        Spacer(Modifier.height(32.dp))
                        HorizontalDivider(color = Color.LightGray.copy(alpha = 0.5f), thickness = 1.dp)
                        Spacer(Modifier.height(24.dp))
                        
                        Text(
                            "Keamanan Akun",
                            fontFamily = poppins,
                            fontSize = 16.sp,
                            color = orange,
                            modifier = Modifier.padding(bottom = 16.dp)
                        )

                        // Kode Registrasi (Tenant Code)
                        OutlinedTextField(
                            value = tenantCode,
                            onValueChange = { tenantCode = it },
                            label = { Text("Kode Registrasi") },
                            placeholder = { Text("Masukkan kode dari pengelola") },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            keyboardOptions = KeyboardOptions(imeAction = ImeAction.Next),
                            keyboardActions = KeyboardActions(onNext = { focus.moveFocus(FocusDirection.Down) }),
                            supportingText = { Text("Wajib diisi sesuai kode perumahan Anda", fontSize = 11.sp) }
                        )

                        Spacer(Modifier.height(12.dp))

                        // Password
                        OutlinedTextField(
                            value = password,
                            onValueChange = { password = it },
                            label = { Text("Buat Kata Sandi") },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            visualTransformation = PasswordVisualTransformation(),
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password, imeAction = ImeAction.Next),
                            keyboardActions = KeyboardActions(onNext = { focus.moveFocus(FocusDirection.Down) }),
                            supportingText = { if (password.isNotBlank() && !isPassValid) Text("Password minimal 6 karakter", color = Color.Red, fontSize = 11.sp) }
                        )

                        Spacer(Modifier.height(12.dp))

                        // Konfirmasi
                        OutlinedTextField(
                            value = confirmPassword,
                            onValueChange = { confirmPassword = it },
                            label = { Text("Ulangi Kata Sandi") },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            visualTransformation = PasswordVisualTransformation(),
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password, imeAction = ImeAction.Done),
                            keyboardActions = KeyboardActions(onDone = { focus.clearFocus() }),
                            supportingText = { if (confirmPassword.isNotBlank() && confirmPassword != password) Text("Konfirmasi kata sandi tidak cocok", color = Color.Red, fontSize = 11.sp) }
                        )

                        Spacer(Modifier.height(32.dp))

                        Button(
                            onClick = {
                                focus.clearFocus()
                                if (!isFormValid || loading) return@Button
                                loading = true
                                scope.launch {
                                    try {
                                        authRepo.register(
                                            fullName.trim(),
                                            email.trim(),
                                            password,
                                            tenantCode.trim(),
                                            googleId.ifBlank { null }
                                        )
                                        loading = false
                                        onGoOtp(email.trim(), "", "", "", "", "")
                                    } catch (e: Throwable) {
                                        loading = false
                                        showError(e)
                                    }
                                }
                            },
                            enabled = isFormValid && !loading,
                            colors = ButtonDefaults.buttonColors(containerColor = orange),
                            shape = RoundedCornerShape(16.dp),
                            modifier = Modifier.fillMaxWidth().height(56.dp)
                        ) {
                            if (loading) {
                                CircularProgressIndicator(modifier = Modifier.size(22.dp), strokeWidth = 2.dp, color = Color.White)
                                Spacer(Modifier.width(12.dp))
                            }
                            Text("Daftar Sekarang", color = Color.White, fontFamily = poppins, fontWeight = FontWeight.Bold, fontSize = 16.sp)
                        }

                        Spacer(Modifier.height(24.dp))

                        Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.Center, verticalAlignment = Alignment.CenterVertically) {
                            Text("Sudah punya akun?", fontSize = 14.sp, color = Color.Gray)
                            Spacer(Modifier.width(6.dp))
                            Text(
                                text = "Masuk di sini",
                                fontSize = 14.sp,
                                fontFamily = poppins,
                                color = blue,
                                modifier = Modifier.clickable(enabled = !loading) { onGoLogin() }
                            )
                        }

                        Spacer(Modifier.height(48.dp)) // Fix cut-off at bottom
                    }
                }
            }
    }
}
