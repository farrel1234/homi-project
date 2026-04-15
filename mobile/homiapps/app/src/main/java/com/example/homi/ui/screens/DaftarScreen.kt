package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
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
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextDecoration
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
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val blue = Color(0xFF256D85)
    val orange = Color(0xFFFFA06B)

    var fullName by remember { mutableStateOf(preName) }
    var email by remember { mutableStateOf(preEmail) }
    var tenantCode by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }

    var passwordVisible by remember { mutableStateOf(false) }
    var confirmPasswordVisible by remember { mutableStateOf(false) }
    var errorText by remember { mutableStateOf<String?>(null) }

    var loading by remember { mutableStateOf(false) }
    val snackbar = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()
    val focus = LocalFocusManager.current
    val scroll = rememberScrollState()

    val api = remember { ApiClient.getApi(tokenStore) }
    val authRepo = remember { AuthRepository(api) }

    val isEmailValid = remember(email) { email.trim().contains("@") && email.trim().contains(".") }
    val isPassValid = remember(password) { password.length >= 6 }
    
    // Dynamic error message
    LaunchedEffect(fullName, email, password, confirmPassword, tenantCode) {
        errorText = when {
            tenantCode.isBlank() -> "Masukkan kode registrasi perumahan."
            fullName.isBlank() -> "Nama lengkap wajib diisi."
            email.isNotBlank() && !isEmailValid -> "Format email tidak valid."
            password.isNotBlank() && !isPassValid -> "Kata sandi minimal 6 karakter."
            confirmPassword.isNotBlank() && password != confirmPassword -> "Konfirmasi kata sandi tidak cocok."
            else -> null
        }
    }

    val isFormValid = fullName.isNotBlank() && isEmailValid && isPassValid && password == confirmPassword && tenantCode.isNotBlank()

    val tfColors = OutlinedTextFieldDefaults.colors(
        focusedBorderColor = blue,
        unfocusedBorderColor = Color.Gray,
        focusedContainerColor = Color(0xFFF8F8F8),
        unfocusedContainerColor = Color(0xFFF8F8F8)
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
        contentWindowInsets = WindowInsets(0.dp) // Edge to edge
    ) { pad ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(pad)
        ) {
            // Background Image
            Image(
                painter = painterResource(id = R.drawable.login), // Use same premium background
                contentDescription = "Background",
                modifier = Modifier.fillMaxSize(),
                contentScale = ContentScale.Crop
            )

            // Back button
            IconButton(
                onClick = onGoLogin,
                modifier = Modifier
                    .padding(top = 40.dp, start = 16.dp)
                    .background(Color.Black.copy(alpha = 0.4f), CircleShape)
            ) {
                Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Kembali", tint = Color.White)
            }

            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .imePadding()
            ) {
                Spacer(modifier = Modifier.weight(1f))

                // Bottom Sheet Container
                Surface(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clip(RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp)),
                    color = Color.White.copy(alpha = 0.95f),
                    tonalElevation = 8.dp
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .verticalScroll(scroll)
                            .padding(horizontal = 24.dp, vertical = 32.dp)
                    ) {
                        Text(
                            text = "Daftar Warga Baru",
                            fontSize = 24.sp,
                            fontWeight = FontWeight.Bold,
                            fontFamily = poppins,
                            color = blue,
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(bottom = 8.dp),
                            textAlign = TextAlign.Center
                        )
                        
                        Text(
                            text = "Silakan lengkapi data untuk bergabung ke perumahan Anda.",
                            fontSize = 13.sp,
                            color = Color.Gray,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.fillMaxWidth().padding(bottom = 24.dp)
                        )

                        // Tenant Code
                        OutlinedTextField(
                            value = tenantCode,
                            onValueChange = { tenantCode = it },
                            label = { Text("Kode Registrasi Perumahan", fontFamily = poppins) },
                            placeholder = { Text("Contoh: LBH002") },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            keyboardOptions = KeyboardOptions(imeAction = ImeAction.Next),
                            keyboardActions = KeyboardActions(onNext = { focus.moveFocus(FocusDirection.Down) })
                        )

                        Spacer(Modifier.height(16.dp))

                        // Full Name
                        OutlinedTextField(
                            value = fullName,
                            onValueChange = { fullName = it },
                            label = { Text("Nama Lengkap", fontFamily = poppins) },
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
                            label = { Text("Email Aktif", fontFamily = poppins) },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email, imeAction = ImeAction.Next),
                            keyboardActions = KeyboardActions(onNext = { focus.moveFocus(FocusDirection.Down) })
                        )

                        Spacer(Modifier.height(16.dp))

                        // Password
                        OutlinedTextField(
                            value = password,
                            onValueChange = { password = it },
                            label = { Text("Buat Kata Sandi", fontFamily = poppins) },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password, imeAction = ImeAction.Next),
                            keyboardActions = KeyboardActions(onNext = { focus.moveFocus(FocusDirection.Down) }),
                            trailingIcon = {
                                IconButton(onClick = { passwordVisible = !passwordVisible }) {
                                    Icon(painter = painterResource(id = if (passwordVisible) R.drawable.show else R.drawable.hide), contentDescription = null, modifier = Modifier.size(24.dp), tint = Color.Gray)
                                }
                            }
                        )

                        Spacer(Modifier.height(16.dp))

                        // Confirm Password
                        OutlinedTextField(
                            value = confirmPassword,
                            onValueChange = { confirmPassword = it },
                            label = { Text("Ketik Ulang Sandi", fontFamily = poppins) },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            visualTransformation = if (confirmPasswordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password, imeAction = ImeAction.Done),
                            keyboardActions = KeyboardActions(onDone = { focus.clearFocus() }),
                            trailingIcon = {
                                IconButton(onClick = { confirmPasswordVisible = !confirmPasswordVisible }) {
                                    Icon(painter = painterResource(id = if (confirmPasswordVisible) R.drawable.show else R.drawable.hide), contentDescription = null, modifier = Modifier.size(24.dp), tint = Color.Gray)
                                }
                            }
                        )

                        Spacer(Modifier.height(32.dp))

                        errorText?.let {
                            Text(
                                it,
                                color = Color.Red,
                                fontSize = 12.sp,
                                textAlign = TextAlign.Center,
                                modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp)
                            )
                        }

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
                                        onGoOtp(email.trim(), tenantCode.trim(), "", "", "", "")
                                    } catch (e: Throwable) {
                                        loading = false
                                        showError(e)
                                    }
                                }
                            },
                            enabled = isFormValid && !loading,
                            colors = ButtonDefaults.buttonColors(
                                containerColor = orange,
                                disabledContainerColor = Color.LightGray.copy(alpha = 0.5f)
                            ),
                            shape = RoundedCornerShape(12.dp),
                            modifier = Modifier.fillMaxWidth().height(56.dp)
                        ) {
                            if (loading) {
                                CircularProgressIndicator(modifier = Modifier.size(22.dp), strokeWidth = 2.dp, color = Color.White)
                                Spacer(Modifier.width(12.dp))
                            }
                            Text(
                                text = if (loading) "Mendaftar..." else "Daftar Sekarang",
                                color = Color.White,
                                fontFamily = poppins,
                                fontWeight = FontWeight.Bold,
                                fontSize = 16.sp
                            )
                        }

                        Spacer(Modifier.height(24.dp))

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.Center,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text("Sudah punya akun?", fontSize = 14.sp, fontFamily = poppins, color = Color.Black)
                            Spacer(Modifier.width(6.dp))
                            Text(
                                text = "Masuk di sini",
                                fontSize = 14.sp,
                                fontFamily = poppins,
                                color = blue,
                                fontWeight = FontWeight.Bold,
                                textDecoration = TextDecoration.Underline,
                                modifier = Modifier.clickable(enabled = !loading) { onGoLogin() }
                            )
                        }

                        Spacer(Modifier.height(32.dp))
                    }
                }
            }
        }
    }
}
