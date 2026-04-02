package com.example.homi.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.outlined.Sms
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

private val OtpBlue = Color(0xFF2F7FA3)
private val OtpBlueDark = Color(0xFF1A5E7B)
private val OtpBorder = Color(0xFFE2E8F0)
private val OtpFieldBg = Color(0xFFF8FAFC)
private val OtpOrange = Color(0xFFF7A477)
private val OtpError = Color(0xFFEF4444)
private val OtpHint = Color(0xFF94A3B8)
private val OtpTextDark = Color(0xFF1E293B)

private val OtpSemi = FontFamily(Font(R.font.poppins_semibold))
private val OtpReg = FontFamily(Font(R.font.poppins_regular))

@Composable
fun LupaKataSandiOtpScreen(
    email: String,
    onBack: () -> Unit,
    onVerifyOtp: suspend (email: String, otp: String) -> String,
    onResendOtp: suspend (email: String) -> Unit,
    onVerified: (resetToken: String) -> Unit
) {
    val scope = rememberCoroutineScope()
    var otp by remember { mutableStateOf("") }
    var loading by remember { mutableStateOf(false) }
    var resendLoading by remember { mutableStateOf(false) }
    var errorText by remember { mutableStateOf<String?>(null) }
    var countdown by remember { mutableStateOf(30) }

    LaunchedEffect(countdown) {
        if (countdown > 0) {
            delay(1000)
            countdown--
        }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(listOf(OtpBlue, OtpBlueDark))
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
                    enabled = !loading && !resendLoading,
                    modifier = Modifier.align(Alignment.CenterStart)
                ) {
                    Icon(Icons.Default.ArrowBack, contentDescription = "Kembali", tint = Color.White)
                }
                Text(
                    text = "Verifikasi OTP",
                    fontFamily = OtpSemi,
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
                    imageVector = Icons.Outlined.Sms,
                    contentDescription = null,
                    tint = Color.White,
                    modifier = Modifier.size(28.dp)
                )
            }

            Spacer(Modifier.height(8.dp))
            Text(
                text = "Masukkan 6 digit kode OTP\nyang dikirim ke email Anda",
                fontFamily = OtpReg,
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
                    .padding(horizontal = 24.dp, vertical = 28.dp),
                verticalArrangement = Arrangement.Top
            ) {
                // Email badge
                Surface(
                    shape = RoundedCornerShape(10.dp),
                    color = OtpBlue.copy(alpha = 0.06f),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(modifier = Modifier.padding(12.dp)) {
                        Text(
                            text = "Email tujuan:",
                            fontFamily = OtpReg,
                            fontSize = 11.sp,
                            color = OtpHint
                        )
                        Text(
                            text = email,
                            fontFamily = OtpSemi,
                            fontSize = 14.sp,
                            color = OtpBlue,
                            fontWeight = FontWeight.Bold
                        )
                    }
                }

                Spacer(Modifier.height(20.dp))

                Text("Kode OTP", fontFamily = OtpSemi, fontSize = 13.sp, color = OtpTextDark)
                Spacer(Modifier.height(6.dp))

                OutlinedTextField(
                    value = otp,
                    onValueChange = {
                        otp = it.filter(Char::isDigit).take(6)
                        errorText = null
                    },
                    placeholder = { Text("_ _ _ _ _ _", fontFamily = OtpReg, color = OtpHint, fontSize = 18.sp, textAlign = TextAlign.Center, modifier = Modifier.fillMaxWidth()) },
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                    enabled = !loading,
                    shape = RoundedCornerShape(14.dp),
                    textStyle = LocalTextStyle.current.copy(
                        fontFamily = OtpSemi,
                        fontSize = 24.sp,
                        color = OtpTextDark,
                        textAlign = TextAlign.Center,
                        letterSpacing = 8.sp
                    ),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = OtpBlue,
                        unfocusedBorderColor = OtpBorder,
                        focusedContainerColor = OtpFieldBg,
                        unfocusedContainerColor = OtpFieldBg,
                        cursorColor = OtpBlue
                    ),
                    modifier = Modifier.fillMaxWidth().height(64.dp)
                )

                Spacer(Modifier.height(12.dp))

                // Resend timer
                Text(
                    text = if (countdown > 0) "Kirim ulang dalam ${countdown}s"
                    else if (resendLoading) "Mengirim ulang..."
                    else "Kirim Ulang Kode OTP",
                    fontFamily = if (countdown == 0 && !resendLoading) OtpSemi else OtpReg,
                    fontSize = 12.sp,
                    color = if (countdown > 0 || resendLoading) OtpHint else OtpBlue,
                    modifier = Modifier
                        .fillMaxWidth()
                        .clickable(enabled = countdown == 0 && !resendLoading && !loading) {
                            resendLoading = true
                            errorText = null
                            scope.launch {
                                runCatching { onResendOtp(email) }
                                    .onSuccess { countdown = 30 }
                                    .onFailure { errorText = it.message ?: "Gagal kirim ulang OTP." }
                                resendLoading = false
                            }
                        },
                    textAlign = TextAlign.End
                )

                if (!errorText.isNullOrBlank()) {
                    Spacer(Modifier.height(16.dp))
                    Surface(
                        shape = RoundedCornerShape(10.dp),
                        color = OtpError.copy(alpha = 0.08f),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(
                            text = errorText.orEmpty(),
                            color = OtpError,
                            fontFamily = OtpReg,
                            fontSize = 12.sp,
                            modifier = Modifier.padding(12.dp)
                        )
                    }
                }

                Spacer(Modifier.height(28.dp))

                Button(
                    onClick = {
                        val code = otp.trim()
                        if (code.length != 6) {
                            errorText = "OTP harus 6 digit."
                            return@Button
                        }
                        loading = true
                        errorText = null
                        scope.launch {
                            runCatching { onVerifyOtp(email, code) }
                                .onSuccess { token -> onVerified(token) }
                                .onFailure { errorText = it.message ?: "Verifikasi OTP gagal." }
                            loading = false
                        }
                    },
                    enabled = !loading && otp.length == 6,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = OtpBlue,
                        disabledContainerColor = OtpBlue.copy(alpha = 0.4f)
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
                        text = if (loading) "Memproses..." else "Verifikasi",
                        color = Color.White,
                        fontFamily = OtpSemi,
                        fontSize = 15.sp
                    )
                }
            }
        }
    }
}
