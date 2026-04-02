package com.example.homi.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.outlined.Lock
import androidx.compose.material.icons.outlined.Visibility
import androidx.compose.material.icons.outlined.VisibilityOff
import androidx.compose.material3.*
import com.example.homi.ui.components.HomiDialog
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.launch

private val ResetBlue = Color(0xFF2F7FA3)
private val ResetBlueDark = Color(0xFF1A5E7B)
private val ResetBorder = Color(0xFFE2E8F0)
private val ResetFieldBg = Color(0xFFF8FAFC)
private val ResetError = Color(0xFFEF4444)
private val ResetHint = Color(0xFF94A3B8)
private val ResetTextDark = Color(0xFF1E293B)
private val SuccessGreen = Color(0xFF22C55E)

private val ResetSemi = FontFamily(Font(R.font.poppins_semibold))
private val ResetReg = FontFamily(Font(R.font.poppins_regular))

@Composable
fun ResetKataSandiBaruScreen(
    onBack: () -> Unit,
    onSubmitReset: suspend (newPassword: String, confirmPassword: String) -> Unit,
    onSuccessGoLogin: () -> Unit
) {
    val scope = rememberCoroutineScope()
    var pass1 by remember { mutableStateOf("") }
    var pass2 by remember { mutableStateOf("") }
    var loading by remember { mutableStateOf(false) }
    var errorText by remember { mutableStateOf<String?>(null) }
    var showSuccess by remember { mutableStateOf(false) }

    var showPass1 by remember { mutableStateOf(false) }
    var showPass2 by remember { mutableStateOf(false) }

    // Strength indicator
    val strength = remember(pass1) {
        when {
            pass1.length < 6 -> 0
            pass1.length < 8 -> 1
            pass1.any { it.isUpperCase() } && pass1.any { it.isDigit() } -> 3
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
        0 -> ResetError
        1 -> Color(0xFFF59E0B)
        2 -> Color(0xFFF7A477)
        3 -> SuccessGreen
        else -> ResetHint
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(listOf(ResetBlue, ResetBlueDark))
            )
            .statusBarsPadding()
    ) {
        // ===== Header =====
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 12.dp, bottom = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Box(modifier = Modifier.fillMaxWidth().padding(horizontal = 8.dp)) {
                IconButton(
                    onClick = onBack,
                    enabled = !loading,
                    modifier = Modifier.align(Alignment.CenterStart)
                ) {
                    Icon(Icons.Default.ArrowBack, contentDescription = "Kembali", tint = Color.White)
                }
                Text(
                    text = "Buat Kata Sandi Baru",
                    fontFamily = ResetSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(12.dp))

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
                text = "Buat kata sandi baru yang kuat\nuntuk mengamankan akun Anda",
                fontFamily = ResetReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                lineHeight = 18.sp
            )
        }

        // ===== White Card =====
        Surface(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            color = Color.White
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .padding(horizontal = 24.dp, vertical = 28.dp)
            ) {
                Text("Kata Sandi Baru", fontFamily = ResetSemi, fontSize = 13.sp, color = ResetTextDark)
                Spacer(Modifier.height(6.dp))

                OutlinedTextField(
                    value = pass1,
                    onValueChange = {
                        pass1 = it
                        errorText = null
                    },
                    placeholder = { Text("Minimal 6 karakter", fontFamily = ResetReg, color = ResetHint, fontSize = 13.sp) },
                    singleLine = true,
                    enabled = !loading,
                    visualTransformation = if (showPass1) VisualTransformation.None else PasswordVisualTransformation(),
                    trailingIcon = {
                        IconButton(onClick = { showPass1 = !showPass1 }) {
                            Icon(
                                imageVector = if (showPass1) Icons.Outlined.Visibility else Icons.Outlined.VisibilityOff,
                                contentDescription = null,
                                tint = ResetHint
                            )
                        }
                    },
                    shape = RoundedCornerShape(14.dp),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = ResetBlue,
                        unfocusedBorderColor = ResetBorder,
                        focusedContainerColor = ResetFieldBg,
                        unfocusedContainerColor = ResetFieldBg,
                        cursorColor = ResetBlue
                    ),
                    modifier = Modifier.fillMaxWidth()
                )

                // Strength
                if (pass1.isNotEmpty()) {
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
                        Text(strengthLabel, color = strengthColor, fontSize = 10.sp, fontFamily = ResetSemi)
                    }
                }

                Spacer(Modifier.height(18.dp))

                Text("Konfirmasi Kata Sandi", fontFamily = ResetSemi, fontSize = 13.sp, color = ResetTextDark)
                Spacer(Modifier.height(6.dp))

                OutlinedTextField(
                    value = pass2,
                    onValueChange = {
                        pass2 = it
                        errorText = null
                    },
                    placeholder = { Text("Ulangi kata sandi baru", fontFamily = ResetReg, color = ResetHint, fontSize = 13.sp) },
                    singleLine = true,
                    enabled = !loading,
                    visualTransformation = if (showPass2) VisualTransformation.None else PasswordVisualTransformation(),
                    trailingIcon = {
                        IconButton(onClick = { showPass2 = !showPass2 }) {
                            Icon(
                                imageVector = if (showPass2) Icons.Outlined.Visibility else Icons.Outlined.VisibilityOff,
                                contentDescription = null,
                                tint = ResetHint
                            )
                        }
                    },
                    shape = RoundedCornerShape(14.dp),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = ResetBlue,
                        unfocusedBorderColor = ResetBorder,
                        focusedContainerColor = ResetFieldBg,
                        unfocusedContainerColor = ResetFieldBg,
                        cursorColor = ResetBlue
                    ),
                    modifier = Modifier.fillMaxWidth()
                )

                // Match indicator
                if (pass2.isNotEmpty() && pass1.isNotEmpty()) {
                    Spacer(Modifier.height(6.dp))
                    val matched = pass1 == pass2
                    Text(
                        text = if (matched) "✓ Password cocok" else "✕ Password tidak sama",
                        color = if (matched) SuccessGreen else ResetError,
                        fontFamily = ResetReg,
                        fontSize = 11.sp
                    )
                }

                if (!errorText.isNullOrBlank()) {
                    Spacer(Modifier.height(16.dp))
                    Surface(
                        shape = RoundedCornerShape(10.dp),
                        color = ResetError.copy(alpha = 0.08f),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(
                            text = errorText.orEmpty(),
                            color = ResetError,
                            fontFamily = ResetReg,
                            fontSize = 12.sp,
                            modifier = Modifier.padding(12.dp)
                        )
                    }
                }

                Spacer(Modifier.height(28.dp))

                Button(
                    onClick = {
                        if (loading) return@Button
                        val p1 = pass1.trim()
                        val p2 = pass2.trim()
                        when {
                            p1.length < 6 -> errorText = "Kata sandi minimal 6 karakter."
                            p1 != p2 -> errorText = "Konfirmasi kata sandi tidak sama."
                            else -> {
                                loading = true
                                errorText = null
                                scope.launch {
                                    runCatching { onSubmitReset(p1, p2) }
                                        .onSuccess { showSuccess = true }
                                        .onFailure { errorText = it.message ?: "Reset password gagal." }
                                    loading = false
                                }
                            }
                        }
                    },
                    enabled = !loading,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = ResetBlue,
                        disabledContainerColor = ResetBlue.copy(alpha = 0.4f)
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
                        text = if (loading) "Memproses..." else "Simpan Password Baru",
                        color = Color.White,
                        fontFamily = ResetSemi,
                        fontSize = 15.sp
                    )
                }
            }
        }
    }

    // ===== Success Dialog =====
    if (showSuccess) {
        HomiDialog(
            onDismissRequest = {},
            title = "Password Berhasil Diubah! 🎉",
            description = "Silakan login kembali menggunakan password baru Anda.",
            icon = Icons.Default.CheckCircle,
            iconTint = SuccessGreen,
            confirmButtonText = "Ke Halaman Login",
            onConfirm = onSuccessGoLogin
        )
    }
}
