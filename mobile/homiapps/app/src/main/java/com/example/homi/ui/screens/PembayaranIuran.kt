// File: app/src/main/java/com/example/homi/ui/screens/PembayaranIuran.kt
package com.example.homi.ui.screens

import android.net.Uri
import android.util.Log
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.PickVisualMediaRequest
import androidx.activity.result.contract.ActivityResultContracts
import androidx.annotation.DrawableRes
import androidx.compose.animation.*
import androidx.compose.foundation.*
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.CloudUpload
import androidx.compose.material.icons.filled.QrCodeScanner
import androidx.compose.material.icons.filled.ReceiptLong
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.SubcomposeAsyncImage
import coil.request.CachePolicy
import coil.request.ImageRequest
import com.example.homi.R
import com.example.homi.ui.components.HomiDialog
import com.example.homi.util.FileUtils
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import retrofit2.HttpException

/* ===== UI TOKENS (Premium Palette) ===== */
private val BlueMain = Color(0xFF2F7FA3)
private val BlueDark = Color(0xFF1E5570)
private val AccentOrange = Color(0xFFE26A2C)
private val SuccessGreen = Color(0xFF22C55E)
private val BgSurface = Color(0xFFF8FAFC)
private val TextDark = Color(0xFF1E293B)
private val TextMuted = Color(0xFF64748B)
private val BorderColor = Color(0xFFE2E8F0)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

@Composable
fun PembayaranIuranScreen(
    amount: String = "Rp25.000",
    bulan: String = "Agustus 2025",
    transaksiId: String = "IPL-123456789",
    qrUrl: String? = null,
    @DrawableRes backIcon: Int = R.drawable.panahkembali,
    onBack: (() -> Unit)? = null,
    onUploadBukti: (suspend (uri: Uri) -> Unit)? = null
) {
    var rincianExpanded by rememberSaveable { mutableStateOf(false) }
    var buktiUri by rememberSaveable { mutableStateOf<Uri?>(null) }
    var isUploading by rememberSaveable { mutableStateOf(false) }
    var uploadMessage by rememberSaveable { mutableStateOf<String?>(null) }
    var showSuccessDialog by rememberSaveable { mutableStateOf(false) }

    val scrollState = rememberScrollState()
    val scope = rememberCoroutineScope()
    val ctx = LocalContext.current

    // Bust cache if qrUrl changes
    val qrUrlBusted = remember(qrUrl) {
        if (qrUrl.isNullOrBlank()) null
        else {
            val sep = if (qrUrl.contains("?")) "&" else "?"
            "$qrUrl${sep}t=${System.currentTimeMillis()}"
        }
    }

    val qrRequest = remember(qrUrlBusted) {
        if (qrUrlBusted.isNullOrBlank()) null
        else {
            ImageRequest.Builder(ctx)
                .data(qrUrlBusted)
                .crossfade(true)
                .memoryCachePolicy(CachePolicy.DISABLED)
                .diskCachePolicy(CachePolicy.DISABLED)
                .networkCachePolicy(CachePolicy.ENABLED)
                .build()
        }
    }

    LaunchedEffect(buktiUri) {
        if (buktiUri != null) {
            delay(150)
            scrollState.animateScrollTo(scrollState.maxValue)
        }
    }

    val picker = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.PickVisualMedia()
    ) { uri ->
        uri?.let { buktiUri = it }
    }

    if (showSuccessDialog) {
        HomiDialog(
            onDismissRequest = { 
                showSuccessDialog = false 
                onBack?.invoke()
            },
            title = "Pembayaran Berhasil",
            description = "Bukti pembayaran Anda telah dikirim dan sedang diverifikasi oleh admin.",
            icon = Icons.Default.CheckCircle,
            iconTint = SuccessGreen,
            confirmButtonText = "Selesai",
            onConfirm = {
                showSuccessDialog = false
                onBack?.invoke()
            }
        )
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Brush.verticalGradient(listOf(BlueMain, BlueDark)))
    ) {
        // ===== HEADER AREA =====
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .statusBarsPadding()
                .padding(top = 16.dp, bottom = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Box(modifier = Modifier.fillMaxWidth().padding(horizontal = 8.dp)) {
                IconButton(onClick = { onBack?.invoke() }, modifier = Modifier.align(Alignment.CenterStart)) {
                    Icon(
                        painter = painterResource(id = backIcon),
                        contentDescription = "Kembali",
                        tint = Color.White,
                        modifier = Modifier.size(24.dp)
                    )
                }
                Text(
                    text = "Konfirmasi Pembayaran",
                    fontFamily = PoppinsSemi,
                    fontSize = 18.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(16.dp))

            // Illustration/Icon Topper
            Surface(
                modifier = Modifier.size(64.dp),
                shape = RoundedCornerShape(20.dp),
                color = Color.White.copy(alpha = 0.15f)
            ) {
                Box(contentAlignment = Alignment.Center) {
                    Icon(
                        imageVector = Icons.Default.ReceiptLong,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(32.dp)
                    )
                }
            }

            Spacer(Modifier.height(12.dp))
            Text(
                text = "Segera selesaikan pembayaran tagihan\nuntuk menghindari denda keterlambatan.",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                lineHeight = 16.sp,
                modifier = Modifier.padding(horizontal = 32.dp)
            )
        }

        // ===== CONTENT CONTAINER =====
        Surface(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            color = Color.White
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(scrollState)
                    .imePadding()
                    .padding(horizontal = 24.dp, vertical = 24.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                // Payment Summary Card
                Surface(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(24.dp),
                    color = BgSurface,
                    border = BorderStroke(1.dp, BorderColor)
                ) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Column {
                                Text("Total Tagihan", fontFamily = PoppinsReg, fontSize = 12.sp, color = TextMuted)
                                Text(amount, fontFamily = PoppinsSemi, fontSize = 24.sp, color = BlueMain)
                            }
                            IconButton(
                                onClick = { rincianExpanded = !rincianExpanded },
                                modifier = Modifier.background(Color.White, CircleShape).size(36.dp)
                            ) {
                                Icon(
                                    imageVector = if (rincianExpanded) Icons.Default.CheckCircle else Icons.Default.ReceiptLong,
                                    contentDescription = null,
                                    modifier = Modifier.size(18.dp),
                                    tint = BlueMain
                                )
                            }
                        }

                        // Expandable Details
                        AnimatedVisibility(
                            visible = rincianExpanded,
                            enter = fadeIn() + expandVertically(),
                            exit = fadeOut() + shrinkVertically()
                        ) {
                            Column(modifier = Modifier.padding(top = 16.dp)) {
                                HorizontalDivider(color = BorderColor, thickness = 1.dp)
                                Spacer(Modifier.height(12.dp))
                                ModernRincianRow("Periode", bulan)
                                ModernRincianRow("ID Transaksi", transaksiId)
                                ModernRincianRow("Metode", "QRIS / Transfer")
                            }
                        }
                    }
                }

                Spacer(Modifier.height(28.dp))

                // QR Section
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.Center
                    ) {
                        Icon(Icons.Default.QrCodeScanner, null, tint = TextDark, modifier = Modifier.size(20.dp))
                        Spacer(Modifier.width(8.dp))
                        Text("Scan QRIS Homi", fontFamily = PoppinsSemi, fontSize = 16.sp, color = TextDark)
                    }
                    
                    Spacer(Modifier.height(16.dp))

                    // QR Image Frame
                    Surface(
                        modifier = Modifier
                            .size(240.dp)
                            .shadow(8.dp, RoundedCornerShape(32.dp)),
                        shape = RoundedCornerShape(32.dp),
                        color = Color.White,
                        border = BorderStroke(1.dp, BorderColor)
                    ) {
                        Box(contentAlignment = Alignment.Center, modifier = Modifier.padding(16.dp)) {
                            if (qrRequest != null) {
                                SubcomposeAsyncImage(
                                    model = qrRequest,
                                    contentDescription = "QR Pembayaran",
                                    modifier = Modifier.fillMaxSize(),
                                    contentScale = ContentScale.Fit,
                                    loading = { CircularProgressIndicator(color = BlueMain, strokeWidth = 2.dp) },
                                    error = {
                                        Text("QR Code\nTidak Tersedia", textAlign = TextAlign.Center, color = TextMuted, fontSize = 11.sp)
                                    }
                                )
                            } else {
                                CircularProgressIndicator(color = BlueMain, strokeWidth = 2.dp)
                            }
                        }
                    }
                }

                Spacer(Modifier.height(32.dp))
                HorizontalDivider(color = BorderColor.copy(alpha = 0.5f))
                Spacer(Modifier.height(24.dp))

                // Upload Proof Section
                Text(
                    text = "Lengkapi Bukti Pembayaran",
                    fontFamily = PoppinsSemi,
                    fontSize = 15.sp,
                    color = TextDark,
                    modifier = Modifier.fillMaxWidth()
                )
                Spacer(Modifier.height(14.dp))

                // Modern Upload Placeholder / Preview
                Surface(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(if (buktiUri == null) 120.dp else 220.dp)
                        .clickable {
                            picker.launch(PickVisualMediaRequest(ActivityResultContracts.PickVisualMedia.ImageOnly))
                        },
                    shape = RoundedCornerShape(20.dp),
                    color = if (buktiUri == null) BgSurface else Color.Black,
                    border = BorderStroke(1.dp, if (buktiUri == null) BorderColor else Color.Transparent)
                ) {
                    if (buktiUri == null) {
                        Column(
                            verticalArrangement = Arrangement.Center,
                            horizontalAlignment = Alignment.CenterHorizontally,
                            modifier = Modifier.fillMaxSize()
                        ) {
                            Icon(Icons.Default.CloudUpload, null, tint = BlueMain, modifier = Modifier.size(32.dp))
                            Spacer(Modifier.height(8.dp))
                            Text("Pilih Foto Bukti / Screenshot", fontFamily = PoppinsReg, fontSize = 13.sp, color = TextMuted)
                        }
                    } else {
                        Box {
                            SubcomposeAsyncImage(
                                model = buktiUri,
                                contentDescription = "Preview Bukti",
                                modifier = Modifier.fillMaxSize(),
                                contentScale = ContentScale.Crop
                            )
                            // Change Overlay
                            Box(
                                modifier = Modifier
                                    .align(Alignment.BottomEnd)
                                    .padding(12.dp)
                                    .background(Color.Black.copy(alpha = 0.6f), CircleShape)
                                    .padding(horizontal = 12.dp, vertical = 6.dp)
                            ) {
                                Text("Ubah Gambar", color = Color.White, fontSize = 10.sp, fontFamily = PoppinsSemi)
                            }
                        }
                    }
                }

                if (buktiUri != null) {
                    TextButton(
                        onClick = { buktiUri = null },
                        modifier = Modifier.padding(top = 4.dp)
                    ) {
                        Text("Hapus Foto", color = Color.Red.copy(alpha = 0.7f), fontSize = 12.sp, fontFamily = PoppinsReg)
                    }
                }

                Spacer(Modifier.height(12.dp))

                uploadMessage?.let { msg ->
                    Surface(
                        color = if (uploadMessage?.contains("gagal", true) == true) Color(0xFFFFEBEE) else BlueMain.copy(alpha = 0.1f),
                        shape = RoundedCornerShape(12.dp),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(
                            text = msg,
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = if (uploadMessage?.contains("gagal", true) == true) Color.Red else BlueMain,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.padding(12.dp)
                        )
                    }
                }

                Spacer(Modifier.height(32.dp))

                // CTA BUTTON
                Button(
                    onClick = {
                        val target = buktiUri ?: return@Button
                        scope.launch {
                            isUploading = true
                            uploadMessage = "Sedang mengirim bukti…"
                            try {
                                onUploadBukti?.invoke(target) ?: error("Handler Belum Siap")
                                uploadMessage = "Berhasil mengirim bukti!"
                                showSuccessDialog = true
                            } catch (e: HttpException) {
                                uploadMessage = "Gagal: ${e.response()?.errorBody()?.string() ?: "Kesalahan Server"}"
                            } catch (e: Exception) {
                                uploadMessage = e.message ?: "Koneksi Bermasalah"
                            } finally {
                                isUploading = false
                            }
                        }
                    },
                    enabled = buktiUri != null && !isUploading && onUploadBukti != null,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(56.dp)
                        .shadow(if (buktiUri != null) 8.dp else 0.dp, RoundedCornerShape(18.dp)),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = AccentOrange,
                        disabledContainerColor = Color(0xFFCBD5E1)
                    ),
                    shape = RoundedCornerShape(18.dp)
                ) {
                    if (isUploading) {
                        CircularProgressIndicator(modifier = Modifier.size(24.dp), color = Color.White, strokeWidth = 2.dp)
                    } else {
                        Text("Kirim Bukti Sekarang", fontFamily = PoppinsSemi, fontSize = 16.sp, color = Color.White)
                    }
                }

                Spacer(Modifier.height(48.dp))
            }
        }
    }
}

@Composable
private fun ModernRincianRow(label: String, value: String) {
    Row(
        modifier = Modifier.fillMaxWidth().padding(vertical = 6.dp),
        horizontalArrangement = Arrangement.SpaceBetween
    ) {
        Text(label, fontFamily = PoppinsReg, fontSize = 13.sp, color = TextMuted)
        Text(value, fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextDark)
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewPembayaranModern() {
    MaterialTheme { 
        PembayaranIuranScreen(qrUrl = null) 
    }
}
