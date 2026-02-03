// File: app/src/main/java/com/example/homi/ui/screens/PembayaranIuran.kt
package com.example.homi.ui.screens

import android.net.Uri
import android.util.Log
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.PickVisualMediaRequest
import androidx.activity.result.contract.ActivityResultContracts
import androidx.annotation.DrawableRes
import androidx.compose.animation.AnimatedVisibility
import androidx.compose.animation.expandVertically
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.animation.shrinkVertically
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
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
import com.example.homi.util.FileUtils
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import retrofit2.HttpException

/* ===== TOKENS ===== */
private val BlueMain = Color(0xFF2F7FA3)
private val BlueText = Color(0xFF2F7FA3)
private val ChipBg = Color(0xFFE6F3F8)
private val TextDark = Color(0xFF0E0E0E)
private val LineGray = Color(0xFFE6E6E6)
private val HintGray = Color(0xFF9AA4AF)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

@Composable
fun PembayaranIuranScreen(
    amount: String = "Rp25.000",
    bulan: String = "Agustus 2025",
    transaksiId: String = "IPL-123456789",
    qrUrl: String? = null,
    @DrawableRes backIcon: Int = R.drawable.panahkembali,
    @DrawableRes qrIcon: Int = R.drawable.qr_code,
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

    // bust cache kalau qrUrl berubah
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
                // QR harus fresh
                .memoryCachePolicy(CachePolicy.DISABLED)
                .diskCachePolicy(CachePolicy.DISABLED)
                .networkCachePolicy(CachePolicy.ENABLED)
                .build()
        }
    }

    LaunchedEffect(qrUrl) {
        Log.d("QR_DEBUG", "qrUrl(raw)=$qrUrl | qrUrl(busted)=$qrUrlBusted")
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
        AlertDialog(
            onDismissRequest = { showSuccessDialog = false },
            title = { Text("Pembayaran Berhasil", fontFamily = PoppinsSemi) },
            text = {
                Text(
                    "Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi admin.",
                    fontFamily = PoppinsReg
                )
            },
            confirmButton = {
                TextButton(
                    onClick = {
                        showSuccessDialog = false
                        onBack?.invoke()
                    }
                ) { Text("OK", fontFamily = PoppinsSemi) }
            }
        )
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        /* ===== TOP BAR ===== */
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            IconButton(
                onClick = { onBack?.invoke() },
                colors = IconButtonDefaults.iconButtonColors(contentColor = Color.White)
            ) {
                Image(
                    painter = painterResource(id = backIcon),
                    contentDescription = "Kembali",
                    modifier = Modifier.size(24.dp)
                )
            }

            Text(
                text = "Pembayaran",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.width(40.dp))
        }

        Text(
            text = "Segera membayar tagihan iuran yang tersedia",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = Color.White,
            textAlign = TextAlign.Center,
            lineHeight = 18.sp,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp)
        )

        /* ===== WHITE CONTAINER ===== */
        Spacer(Modifier.height(16.dp))
        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(scrollState)
                    .imePadding()
                    .navigationBarsPadding()
                    .padding(horizontal = 14.dp, vertical = 12.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {

                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 6.dp, vertical = 6.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Row(
                        modifier = Modifier
                            .clip(RoundedCornerShape(18.dp))
                            .background(ChipBg)
                            .padding(horizontal = 12.dp, vertical = 8.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(
                            text = amount,
                            fontFamily = PoppinsSemi,
                            fontSize = 14.sp,
                            color = BlueText
                        )
                    }

                    TextButton(
                        onClick = { rincianExpanded = !rincianExpanded },
                        contentPadding = PaddingValues(horizontal = 8.dp, vertical = 0.dp)
                    ) {
                        Text(
                            text = if (rincianExpanded) "Rincian ▴" else "Rincian ▾",
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = TextDark
                        )
                    }
                }

                Divider(color = LineGray, thickness = 1.dp)

                AnimatedVisibility(
                    visible = rincianExpanded,
                    enter = fadeIn() + expandVertically(),
                    exit = shrinkVertically() + fadeOut()
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(top = 8.dp, bottom = 6.dp)
                    ) {
                        Box(Modifier.fillMaxWidth()) {
                            Divider(
                                color = LineGray,
                                thickness = 1.dp,
                                modifier = Modifier.align(Alignment.Center)
                            )
                            Text(
                                text = "Rincian Pembayaran",
                                fontFamily = PoppinsSemi,
                                fontSize = 12.sp,
                                color = HintGray,
                                modifier = Modifier
                                    .align(Alignment.Center)
                                    .background(Color.White)
                                    .padding(horizontal = 10.dp)
                            )
                        }

                        Spacer(Modifier.height(8.dp))
                        RincianRow("Total Pembayaran", amount, highlightRight = true)
                        Divider(color = LineGray, thickness = 1.dp)
                        RincianRow("Bulan", bulan)
                        Divider(color = LineGray, thickness = 1.dp)
                        RincianRow("Transaksi ID", transaksiId)
                        Divider(color = LineGray, thickness = 1.dp)
                    }
                }

                Spacer(Modifier.height(10.dp))

                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Image(
                        painter = painterResource(id = R.drawable.ic_qr),
                        contentDescription = null,
                        modifier = Modifier
                            .size(16.dp)
                            .clip(CircleShape),
                        contentScale = ContentScale.Crop
                    )
                    Spacer(Modifier.height(6.dp))
                    Text(text = "QRIS", fontFamily = PoppinsSemi, fontSize = 14.sp, color = TextDark)
                    Spacer(Modifier.height(8.dp))

                    // ===== QR IMAGE (ANTI-BLANK) =====
                    if (qrRequest != null) {
                        SubcomposeAsyncImage(
                            model = qrRequest,
                            contentDescription = "QR Pembayaran",
                            modifier = Modifier.size(220.dp).padding(2.dp),
                            contentScale = ContentScale.Fit,
                            onError = { st ->
                                Log.e("QR_DEBUG", "QR throwable=${st.result.throwable}", st.result.throwable)
                            },
                            loading = { /* ... */ },
                            error = {
                                Image(
                                    painter = painterResource(qrIcon),
                                    contentDescription = "QR Pembayaran (fallback)",
                                    modifier = Modifier.size(220.dp).padding(2.dp),
                                    contentScale = ContentScale.Fit
                                )
                                Log.e("QR_DEBUG", "QR failed to load. url=$qrUrlBusted")
                            }
                        )

                    } else {
                        Image(
                            painter = painterResource(qrIcon),
                            contentDescription = "QR Pembayaran",
                            modifier = Modifier
                                .size(220.dp)
                                .padding(2.dp),
                            contentScale = ContentScale.Fit
                        )
                    }
                }

                Spacer(Modifier.height(22.dp))
                Divider(color = LineGray, thickness = 1.dp)
                Spacer(Modifier.height(12.dp))

                Column(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Text(
                        text = "Upload Bukti Pembayaran",
                        fontFamily = PoppinsSemi,
                        fontSize = 14.sp,
                        color = TextDark,
                        textAlign = TextAlign.Center
                    )

                    Spacer(Modifier.height(10.dp))

                    Row(
                        horizontalArrangement = Arrangement.Center,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        OutlinedButton(
                            onClick = {
                                picker.launch(
                                    PickVisualMediaRequest(ActivityResultContracts.PickVisualMedia.ImageOnly)
                                )
                            },
                            border = ButtonDefaults.outlinedButtonBorder.copy(width = 1.dp),
                        ) {
                            Text(
                                text = if (buktiUri == null) "Pilih Gambar" else "Ganti Gambar",
                                fontFamily = PoppinsReg
                            )
                        }

                        if (buktiUri != null) {
                            Spacer(Modifier.width(8.dp))
                            TextButton(onClick = { buktiUri = null }) {
                                Text("Hapus", fontFamily = PoppinsReg, color = Color(0xFFDC2626))
                            }
                        }
                    }

                    AnimatedVisibility(visible = buktiUri != null) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Spacer(Modifier.height(10.dp))
                            SubcomposeAsyncImage(
                                model = buktiUri,
                                contentDescription = "Preview Bukti",
                                modifier = Modifier
                                    .size(width = 260.dp, height = 180.dp)
                                    .clip(RoundedCornerShape(14.dp)),
                                contentScale = ContentScale.Crop,
                                loading = {
                                    Box(
                                        Modifier
                                            .size(width = 260.dp, height = 180.dp),
                                        contentAlignment = Alignment.Center
                                    ) {
                                        CircularProgressIndicator()
                                    }
                                }
                            )
                        }
                    }

                    uploadMessage?.let { msg ->
                        Spacer(Modifier.height(8.dp))
                        Text(
                            text = msg,
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = HintGray,
                            textAlign = TextAlign.Center
                        )
                    }

                    Spacer(Modifier.height(14.dp))

                    Button(
                        onClick = {
                            val target = buktiUri ?: return@Button
                            scope.launch {
                                isUploading = true
                                uploadMessage = "Mengunggah bukti…"

                                try {
                                    // optional debug
                                    val debugPart = FileUtils.uriToMultipart(ctx, target)
                                    Log.d("UPLOAD_DEBUG", "debugPart.headers=${debugPart.headers}")

                                    onUploadBukti?.invoke(target) ?: error("Upload handler belum diset")

                                    uploadMessage = "Bukti terkirim. Menunggu verifikasi admin."
                                    showSuccessDialog = true
                                } catch (e: HttpException) {
                                    val body = e.response()?.errorBody()?.string()
                                    uploadMessage = body ?: "Upload gagal (HTTP ${e.code()})"
                                } catch (e: Exception) {
                                    uploadMessage = e.message ?: "Upload gagal"
                                } finally {
                                    isUploading = false
                                }
                            }
                        },
                        enabled = buktiUri != null && !isUploading && onUploadBukti != null,
                        modifier = Modifier
                            .fillMaxWidth(0.9f)
                            .height(48.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = BlueMain)
                    ) {
                        Text(
                            text = if (isUploading) "Mengunggah…" else "Kirim Bukti",
                            fontFamily = PoppinsSemi,
                            color = Color.White
                        )
                    }
                }

                Spacer(Modifier.height(24.dp))
            }
        }
    }
}

@Composable
private fun RincianRow(left: String, right: String, highlightRight: Boolean = false) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 6.dp, vertical = 10.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = left,
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = TextDark,
            modifier = Modifier.weight(1f)
        )
        Text(
            text = right,
            fontFamily = if (highlightRight) PoppinsSemi else PoppinsReg,
            fontSize = 12.sp,
            color = if (highlightRight) BlueText else Color(0xFF475569),
            textAlign = TextAlign.End,
            modifier = Modifier.weight(1f)
        )
    }
}

@Preview(showBackground = true, showSystemUi = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewPembayaran() {
    MaterialTheme { PembayaranIuranScreen(qrUrl = null, qrIcon = R.drawable.qr_code) }
}
