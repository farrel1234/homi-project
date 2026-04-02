package com.example.homi.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Lock
import androidx.compose.material.icons.outlined.Visibility
import androidx.compose.material.icons.outlined.VisibilityOff
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.repository.AccountRepository
import kotlinx.coroutines.launch

private val BlueMain = Color(0xFF2F7FA3)
private val BlueDark = Color(0xFF1A5E7B)
private val AccentOrange = Color(0xFFF7A477)
private val FieldBg = Color(0xFFF8FAFC)
private val FieldBorder = Color(0xFFE2E8F0)
private val TextDark = Color(0xFF1E293B)
private val HintGray = Color(0xFF94A3B8)
private val SuccessGreen = Color(0xFF22C55E)
private val ErrorRed = Color(0xFFEF4444)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

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

    var showCurrent by remember { mutableStateOf(false) }
    var showNew by remember { mutableStateOf(false) }
    var showConfirm by remember { mutableStateOf(false) }
    var errorText by remember { mutableStateOf<String?>(null) }
    var successText by remember { mutableStateOf<String?>(null) }

    // Password strength
    val strength = remember(newPassword) {
        when {
            newPassword.length < 6 -> 0
            newPassword.length < 8 -> 1
            newPassword.any { it.isUpperCase() } && newPassword.any { it.isDigit() } -> 3
            else -> 2
        }
    }
    val strengthLabel = when (strength) {
        0 -> "Terlalu Pendek"
        1 -> "Lemah"
        2 -> "Sedang"
        3 -> "Kuat"
        else -> ""
    }
    val strengthColor = when (strength) {
        0 -> ErrorRed
        1 -> Color(0xFFF59E0B)
        2 -> AccentOrange
        3 -> SuccessGreen
        else -> HintGray
    }

    Scaffold(
        snackbarHost = { SnackbarHost(snackbar) }
    ) { pad ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(pad)
                .background(
                    Brush.verticalGradient(
                        colors = listOf(BlueMain, BlueDark)
                    )
                )
        ) {
            // ===== Header =====
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .statusBarsPadding()
                    .padding(top = 16.dp, bottom = 28.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Box(modifier = Modifier.fillMaxWidth().padding(horizontal = 8.dp)) {
                    IconButton(
                        onClick = onBack,
                        modifier = Modifier.align(Alignment.CenterStart)
                    ) {
                        Icon(
                            painter = painterResource(R.drawable.panahkembali),
                            contentDescription = "Kembali",
                            tint = Color.White,
                            modifier = Modifier.size(22.dp)
                        )
                    }

                    Text(
                        text = "Ubah Kata Sandi",
                        fontFamily = PoppinsSemi,
                        fontSize = 20.sp,
                        color = Color.White,
                        modifier = Modifier.align(Alignment.Center)
                    )
                }

                Spacer(Modifier.height(8.dp))

                // Security icon
                Box(
                    modifier = Modifier
                        .size(56.dp)
                        .background(Color.White.copy(alpha = 0.15f), RoundedCornerShape(16.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Outlined.Lock,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(28.dp)
                    )
                }

                Spacer(Modifier.height(8.dp))
                Text(
                    text = "Perbarui keamanan akun Anda",
                    fontFamily = PoppinsReg,
                    fontSize = 13.sp,
                    color = Color.White.copy(0.8f),
                    textAlign = TextAlign.Center
                )
            }

            // ===== White Card =====
            Card(
                modifier = Modifier.fillMaxSize(),
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState())
                        .padding(horizontal = 20.dp, vertical = 24.dp)
                ) {
                    // Error / Success Messages
                    errorText?.let {
                        Surface(
                            shape = RoundedCornerShape(12.dp),
                            color = ErrorRed.copy(alpha = 0.08f),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Text(
                                text = it,
                                color = ErrorRed,
                                fontFamily = PoppinsReg,
                                fontSize = 12.sp,
                                modifier = Modifier.padding(12.dp)
                            )
                        }
                        Spacer(Modifier.height(12.dp))
                    }

                    successText?.let {
                        Surface(
                            shape = RoundedCornerShape(12.dp),
                            color = SuccessGreen.copy(alpha = 0.08f),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Text(
                                text = it,
                                color = SuccessGreen,
                                fontFamily = PoppinsReg,
                                fontSize = 12.sp,
                                modifier = Modifier.padding(12.dp)
                            )
                        }
                        Spacer(Modifier.height(12.dp))
                    }

                    // Current Password
                    Text("Kata Sandi Saat Ini", fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextDark)
                    Spacer(Modifier.height(6.dp))
                    OutlinedTextField(
                        value = currentPassword,
                        onValueChange = { currentPassword = it; errorText = null; successText = null },
                        placeholder = { Text("Masukkan kata sandi saat ini", fontFamily = PoppinsReg, color = HintGray, fontSize = 13.sp) },
                        visualTransformation = if (showCurrent) VisualTransformation.None else PasswordVisualTransformation(),
                        trailingIcon = {
                            IconButton(onClick = { showCurrent = !showCurrent }) {
                                Icon(
                                    imageVector = if (showCurrent) Icons.Outlined.Visibility else Icons.Outlined.VisibilityOff,
                                    contentDescription = null,
                                    tint = HintGray
                                )
                            }
                        },
                        singleLine = true,
                        shape = RoundedCornerShape(14.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = BlueMain,
                            unfocusedBorderColor = FieldBorder,
                            focusedContainerColor = FieldBg,
                            unfocusedContainerColor = FieldBg,
                            cursorColor = BlueMain
                        ),
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(Modifier.height(18.dp))

                    // New Password
                    Text("Kata Sandi Baru", fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextDark)
                    Spacer(Modifier.height(6.dp))
                    OutlinedTextField(
                        value = newPassword,
                        onValueChange = { newPassword = it; errorText = null; successText = null },
                        placeholder = { Text("Minimal 6 karakter", fontFamily = PoppinsReg, color = HintGray, fontSize = 13.sp) },
                        visualTransformation = if (showNew) VisualTransformation.None else PasswordVisualTransformation(),
                        trailingIcon = {
                            IconButton(onClick = { showNew = !showNew }) {
                                Icon(
                                    imageVector = if (showNew) Icons.Outlined.Visibility else Icons.Outlined.VisibilityOff,
                                    contentDescription = null,
                                    tint = HintGray
                                )
                            }
                        },
                        singleLine = true,
                        shape = RoundedCornerShape(14.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = BlueMain,
                            unfocusedBorderColor = FieldBorder,
                            focusedContainerColor = FieldBg,
                            unfocusedContainerColor = FieldBg,
                            cursorColor = BlueMain
                        ),
                        modifier = Modifier.fillMaxWidth()
                    )

                    // Password strength indicator
                    if (newPassword.isNotEmpty()) {
                        Spacer(Modifier.height(8.dp))
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            repeat(3) { idx ->
                                Box(
                                    modifier = Modifier
                                        .weight(1f)
                                        .height(4.dp)
                                        .padding(end = if (idx < 2) 4.dp else 0.dp)
                                        .background(
                                            if (idx < strength) strengthColor else Color(0xFFE2E8F0),
                                            RoundedCornerShape(2.dp)
                                        )
                                )
                            }
                            Spacer(Modifier.width(10.dp))
                            Text(
                                text = strengthLabel,
                                color = strengthColor,
                                fontSize = 10.sp,
                                fontFamily = PoppinsSemi
                            )
                        }
                    }

                    Spacer(Modifier.height(18.dp))

                    // Confirm Password
                    Text("Konfirmasi Kata Sandi", fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextDark)
                    Spacer(Modifier.height(6.dp))
                    OutlinedTextField(
                        value = confirmPassword,
                        onValueChange = { confirmPassword = it; errorText = null; successText = null },
                        placeholder = { Text("Ulangi kata sandi baru", fontFamily = PoppinsReg, color = HintGray, fontSize = 13.sp) },
                        visualTransformation = if (showConfirm) VisualTransformation.None else PasswordVisualTransformation(),
                        trailingIcon = {
                            IconButton(onClick = { showConfirm = !showConfirm }) {
                                Icon(
                                    imageVector = if (showConfirm) Icons.Outlined.Visibility else Icons.Outlined.VisibilityOff,
                                    contentDescription = null,
                                    tint = HintGray
                                )
                            }
                        },
                        singleLine = true,
                        shape = RoundedCornerShape(14.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = BlueMain,
                            unfocusedBorderColor = FieldBorder,
                            focusedContainerColor = FieldBg,
                            unfocusedContainerColor = FieldBg,
                            cursorColor = BlueMain
                        ),
                        modifier = Modifier.fillMaxWidth()
                    )

                    // Match indicator
                    if (confirmPassword.isNotEmpty() && newPassword.isNotEmpty()) {
                        Spacer(Modifier.height(6.dp))
                        val matched = newPassword == confirmPassword
                        Text(
                            text = if (matched) "✓ Password cocok" else "✕ Password tidak sama",
                            color = if (matched) SuccessGreen else ErrorRed,
                            fontFamily = PoppinsReg,
                            fontSize = 11.sp
                        )
                    }

                    Spacer(Modifier.height(28.dp))

                    // Submit Button
                    Button(
                        onClick = {
                            errorText = null
                            successText = null

                            if (currentPassword.isBlank() || newPassword.isBlank() || confirmPassword.isBlank()) {
                                errorText = "Semua kolom wajib diisi."
                                return@Button
                            }
                            if (newPassword.length < 6) {
                                errorText = "Password baru minimal 6 karakter."
                                return@Button
                            }
                            if (newPassword != confirmPassword) {
                                errorText = "Konfirmasi password tidak cocok."
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
                                    val ok = res.success == true
                                    val msg = res.message?.takeIf { it.isNotBlank() }

                                    if (ok) {
                                        successText = msg ?: "Password berhasil diubah!"
                                        currentPassword = ""
                                        newPassword = ""
                                        confirmPassword = ""
                                    } else {
                                        errorText = msg ?: "Gagal mengubah password."
                                    }
                                } catch (e: Exception) {
                                    loading = false
                                    errorText = e.message ?: "Terjadi kesalahan."
                                }
                            }
                        },
                        enabled = !loading,
                        colors = ButtonDefaults.buttonColors(
                            containerColor = BlueMain,
                            disabledContainerColor = BlueMain.copy(alpha = 0.5f)
                        ),
                        shape = RoundedCornerShape(16.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(52.dp),
                        elevation = ButtonDefaults.buttonElevation(defaultElevation = 4.dp)
                    ) {
                        if (loading) {
                            CircularProgressIndicator(
                                modifier = Modifier.size(20.dp),
                                strokeWidth = 2.dp,
                                color = Color.White
                            )
                            Spacer(Modifier.width(10.dp))
                        }
                        Text(
                            text = if (loading) "Memproses..." else "Simpan Perubahan",
                            fontFamily = PoppinsSemi,
                            fontSize = 15.sp,
                            color = Color.White
                        )
                    }

                    Spacer(Modifier.height(12.dp))

                    TextButton(
                        onClick = onBack,
                        modifier = Modifier.align(Alignment.CenterHorizontally)
                    ) {
                        Text("Batal", fontFamily = PoppinsReg, color = HintGray, fontSize = 14.sp)
                    }
                }
            }
        }
    }
}
