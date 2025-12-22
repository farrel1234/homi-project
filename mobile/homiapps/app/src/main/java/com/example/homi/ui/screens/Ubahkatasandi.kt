package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.statusBarsPadding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.delay
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation

/* ===== Tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueBorder   = Color(0xFF2F7FA3)
private val FieldBg      = Color(0xFFF1F2F4)
private val TextDark     = Color(0xFF0E0E0E)
private val HintGray     = Color(0xFF8A8A8A)
private val AccentOrange = Color(0xFFFFA06B)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun UbahKataSandiScreen(
    onBack: (() -> Unit)? = null,
    onSelesai: (() -> Unit)? = null,                 // opsional (kalau mau popBack)
    onLupaKataSandi: (() -> Unit)? = null,           // ✅ akan dipakai setelah sukses simpan
    @DrawableRes backIcon: Int = R.drawable.panahkembali,
    @DrawableRes successImage: Int = R.drawable.bahagia,
    @DrawableRes bellIcon: Int = R.drawable.notif
) {
    var oldPass by remember { mutableStateOf("") }
    var newPass by remember { mutableStateOf("") }
    var confirmPass by remember { mutableStateOf("") }

    var showOld by remember { mutableStateOf(false) }
    var showNew by remember { mutableStateOf(false) }
    var showConfirm by remember { mutableStateOf(false) }

    var errorText by remember { mutableStateOf<String?>(null) }
    var showPopup by remember { mutableStateOf(false) }

    Box(Modifier.fillMaxSize()) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(BlueMain)
                .statusBarsPadding()
        ) {
            /* Header */
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Image(
                    painter = painterResource(backIcon),
                    contentDescription = "Kembali",
                    modifier = Modifier
                        .size(24.dp)
                        .clip(CircleShape)
                        .clickable(enabled = onBack != null) { onBack?.invoke() }
                )
                Spacer(Modifier.width(8.dp))
                Text(
                    text = "Ubah Kata Sandi",
                    fontFamily = PoppinsSemi,
                    fontSize = 22.sp,
                    color = Color.White,
                    modifier = Modifier.weight(1f),
                    textAlign = TextAlign.Center
                )
                Spacer(Modifier.width(24.dp))
            }

            /* White sheet */
            Spacer(Modifier.height(10.dp))
            Surface(
                color = Color.White,
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                modifier = Modifier.fillMaxSize()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState())
                        .imePadding()
                        .navigationBarsPadding()
                        .padding(horizontal = 16.dp, vertical = 18.dp)
                ) {

                Label("Masukkan kata sandi lama")
                    PasswordField(
                        value = oldPass,
                        onValueChange = { oldPass = it; errorText = null },
                        visible = showOld,
                        onToggleVisible = { showOld = !showOld }
                    )

                    Spacer(Modifier.height(14.dp))
                    Label("Buat kata sandi baru")
                    PasswordField(
                        value = newPass,
                        onValueChange = { newPass = it; errorText = null },
                        visible = showNew,
                        onToggleVisible = { showNew = !showNew }
                    )

                    Spacer(Modifier.height(14.dp))
                    Label("Masukkan ulang kata sandi baru")
                    PasswordField(
                        value = confirmPass,
                        onValueChange = { confirmPass = it; errorText = null },
                        visible = showConfirm,
                        onToggleVisible = { showConfirm = !showConfirm }
                    )

                    // —— Lupa kata sandi? (kanan bawah)
                    Spacer(Modifier.height(6.dp))
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.End
                    ) {
                        Text(
                            text = "Lupa kata sandi?",
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = Color(0xFF6B7280),
                            textDecoration = TextDecoration.Underline,
                            modifier = Modifier.clickable { onLupaKataSandi?.invoke() }
                        )
                    }

                    errorText?.let {
                        Spacer(Modifier.height(8.dp))
                        Text(
                            text = it,
                            color = Color(0xFFD32F2F),
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp
                        )
                    }

                    Spacer(Modifier.height(20.dp))
                    Button(
                        onClick = {
                            when {
                                oldPass.isBlank() || newPass.isBlank() || confirmPass.isBlank() ->
                                    errorText = "Semua kolom wajib diisi."
                                newPass != confirmPass ->
                                    errorText = "Konfirmasi kata sandi tidak cocok."
                                newPass.length < 6 ->
                                    errorText = "Kata sandi baru minimal 6 karakter."
                                else -> {
                                    errorText = null
                                    showPopup = true
                                }
                            }
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = AccentOrange),
                        shape = RoundedCornerShape(10.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(48.dp)
                    ) {
                        Text(
                            text = "Simpan",
                            color = Color.White,
                            fontFamily = PoppinsSemi,
                            fontSize = 15.sp
                        )
                    }
                }
            }
        }

        /* POPUP SUKSES -> setelah 2 detik pindah ke LupaKataSandiEmailScreen */
        if (showPopup) {
            SuccessPopup(
                successImage = successImage,
                bellIcon = bellIcon,
                message = "Kata Sandi Berhasil\nDi Ganti !",
                onFinished = {
                    showPopup = false

                    // reset field (opsional)
                    oldPass = ""
                    newPass = ""
                    confirmPass = ""

                    // ✅ INI YANG KAMU MAU:
                    // setelah sukses simpan -> pindah ke layar Lupa Kata Sandi (Email)
                    if (onLupaKataSandi != null) {
                        onLupaKataSandi.invoke()
                    } else {
                        // fallback kalau belum disambung ke NavHost
                        onSelesai?.invoke()
                    }
                }
            )
        }
    }
}

/* ---------- Sub-components ---------- */

@Composable
private fun Label(text: String) {
    Text(
        text = text,
        fontFamily = PoppinsSemi,
        fontSize = 14.sp,
        color = BlueMain
    )
    Spacer(Modifier.height(6.dp))
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun PasswordField(
    value: String,
    onValueChange: (String) -> Unit,
    visible: Boolean,
    onToggleVisible: () -> Unit
) {
    TextField(
        value = value,
        onValueChange = onValueChange,
        singleLine = true,
        visualTransformation = if (visible) VisualTransformation.None else PasswordVisualTransformation(),
        trailingIcon = {
            IconButton(onClick = onToggleVisible) {
                Icon(
                    imageVector = if (visible) Icons.Filled.VisibilityOff else Icons.Filled.Visibility,
                    contentDescription = if (visible) "Sembunyikan" else "Tampilkan"
                )
            }
        },
        shape = RoundedCornerShape(10.dp),
        textStyle = LocalTextStyle.current.copy(
            fontFamily = PoppinsReg,
            fontSize = 14.sp,
            color = TextDark
        ),
        colors = TextFieldDefaults.colors(
            focusedContainerColor = FieldBg,
            unfocusedContainerColor = FieldBg,
            focusedIndicatorColor = BlueBorder,
            unfocusedIndicatorColor = BlueBorder,
            cursorColor = BlueBorder
        ),
        placeholder = {
            Text("••••••", color = HintGray, fontFamily = PoppinsReg)
        },
        modifier = Modifier.fillMaxWidth()
    )
}

/* Popup sukses */
@Composable
private fun SuccessPopup(
    @DrawableRes successImage: Int,
    @DrawableRes bellIcon: Int,
    message: String,
    onFinished: () -> Unit
) {
    LaunchedEffect(Unit) {
        delay(2000) // 2 detik
        onFinished()
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0x99000000))
            .clickable(
                interactionSource = remember { MutableInteractionSource() },
                indication = null
            ) { },
        contentAlignment = Alignment.Center
    ) {
        Box(contentAlignment = Alignment.TopCenter) {

            Card(
                shape = RoundedCornerShape(22.dp),
                border = CardDefaults.outlinedCardBorder().copy(
                    width = 2.dp,
                    brush = androidx.compose.ui.graphics.SolidColor(BlueBorder)
                ),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 10.dp),
                modifier = Modifier
                    .fillMaxWidth(0.86f)
                    .widthIn(max = 360.dp)
                    .padding(top = 44.dp)
                    .defaultMinSize(minHeight = 420.dp)
            ) {
                Column(
                    modifier = Modifier
                        .padding(horizontal = 22.dp, vertical = 22.dp)
                        .fillMaxWidth(),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Image(
                        painter = painterResource(successImage),
                        contentDescription = "Sukses",
                        contentScale = ContentScale.Fit,
                        modifier = Modifier.size(200.dp)
                    )
                    Spacer(Modifier.height(18.dp))
                    Text(
                        text = message,
                        fontFamily = PoppinsSemi,
                        fontSize = 18.sp,
                        color = TextDark,
                        textAlign = TextAlign.Center,
                        lineHeight = 24.sp
                    )
                    Spacer(Modifier.height(8.dp))
                }
            }

            // Badge lonceng di atas kartu
            Box(
                modifier = Modifier
                    .offset(y = (-22).dp)
                    .size(82.dp),
                contentAlignment = Alignment.Center
            ) {
                Box(
                    modifier = Modifier
                        .size(82.dp)
                        .clip(CircleShape)
                        .background(Color.White)
                )
                Box(
                    modifier = Modifier
                        .size(66.dp)
                        .clip(CircleShape)
                        .background(BlueMain),
                    contentAlignment = Alignment.Center
                ) {
                    Image(
                        painter = painterResource(bellIcon),
                        contentDescription = "Notifikasi",
                        modifier = Modifier.size(30.dp)
                    )
                    Box(
                        modifier = Modifier
                            .align(Alignment.TopEnd)
                            .offset(x = 6.dp, y = (-6).dp)
                            .size(18.dp)
                            .clip(CircleShape)
                            .background(Color.White),
                        contentAlignment = Alignment.Center
                    ) {
                        Box(
                            modifier = Modifier
                                .size(14.dp)
                                .clip(CircleShape)
                                .background(Color(0xFFFF9966)),
                            contentAlignment = Alignment.Center
                        ) {
                            Text(
                                "1",
                                color = Color.White,
                                fontFamily = PoppinsSemi,
                                fontSize = 10.sp
                            )
                        }
                    }
                }
            }
        }
    }
}

/* Preview */
@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewUbahKataSandi() {
    MaterialTheme {
        UbahKataSandiScreen()
    }
}
