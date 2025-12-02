package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

/* ========= THEME ========= */
private val BlueMain = Color(0xFF2F79A0)
private val BlueLine = Color(0xFFFFFFFF)
private val BlueBorder = Color(0xFF2F79A0)
private val AccentOrange = Color(0xFFFF9966)
private val TextPrimary = Color(0xFF000000)
private val TextMuted = Color(0xFF8A8A8A)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

@Composable
fun ProsesPengajuan2Screen(
    nomorPengajuan: String = "001",
    nama: String = "Lily",
    jenisPengajuan: String = "Surat Keterangan",
    tanggalPengajuan: String = "01 Oktober 2021",
    onBack: (() -> Unit)? = null,
    onWhatsappClick: (() -> Unit)? = null,
    // pakai ikon barumu
    @DrawableRes icBack: Int = R.drawable.panahkembali,
    @DrawableRes icStepPengajuan: Int = R.drawable.ic_pengajuan_aktif2,
    @DrawableRes icStepProses: Int = R.drawable.ic_proses2,
    @DrawableRes icStepSelesai: Int = R.drawable.ic_selesai,
    @DrawableRes icDetailHeader: Int = R.drawable.ic_detail_header,
    @DrawableRes icNama: Int = R.drawable.ic_user,
    @DrawableRes icJenis: Int = R.drawable.ic_doc,
    @DrawableRes icTanggal: Int = R.drawable.ic_calendar
) {
    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
    ) {
        Column(Modifier.fillMaxSize()) {

            /* ===== TOP BAR ===== */
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .statusBarsPadding()
                    .padding(horizontal = 16.dp, vertical = 10.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Image(
                    painter = painterResource(icBack),
                    contentDescription = "Kembali",
                    modifier = Modifier
                        .size(24.dp)
                        .clip(CircleShape)
                        .clickable(enabled = onBack != null) { onBack?.invoke() }
                )
                Spacer(Modifier.width(8.dp))
                Spacer(Modifier.height(16.dp))
                Text(
                    text = "Pengajuan Layanan",
                    fontFamily = PoppinsSemi,
                    fontSize = 22.sp,
                    color = Color.White,
                    modifier = Modifier.weight(1f),
                    textAlign = TextAlign.Center
                )
                Spacer(Modifier.width(24.dp))
            }

            Spacer(Modifier.height(8.dp))

            /* ===== STEPPER ===== */
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 24.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Step 1: Pengajuan Layanan
                StepItem(
                    icon = icStepPengajuan,
                    label = "Pengajuan\nLayanan",
                    circleColor = Color.White
                )

                StepConnector()

                // Step 2: Sedang Diproses
                StepItem(
                    icon = icStepProses,
                    label = "Sedang\nDiproses",
                    circleColor = AccentOrange
                )

                StepConnector()

                // Step 3: Pengajuan Selesai
                StepItem(
                    icon = icStepSelesai,
                    label = "Pengajuan\nSelesai",
                    circleColor = Color.White
                )
            }

            Spacer(Modifier.height(18.dp))

            /* ===== NOMOR PENGAJUAN ===== */
            Column(
                modifier = Modifier.fillMaxWidth(),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Text(
                    text = "Nomor Pengajuan",
                    fontFamily = PoppinsReg,
                    fontSize = 14.sp,
                    color = Color.White
                )
                Text(
                    text = nomorPengajuan,
                    fontFamily = PoppinsSemi,
                    fontWeight = FontWeight.Bold,
                    fontSize = 32.sp,
                    color = Color.White
                )
            }

            Spacer(Modifier.height(18.dp))

            /* ===== KONTEN PUTIH ===== */
            Card(
                modifier = Modifier.fillMaxSize(),
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 16.dp, vertical = 20.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {

                    // Card Status
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(16.dp),
                        border = BorderStroke(2.dp, BlueBorder),
                        colors = CardDefaults.cardColors(containerColor = Color.White)
                    ) {
                        Column(
                            horizontalAlignment = Alignment.CenterHorizontally,
                            modifier = Modifier.padding(horizontal = 16.dp, vertical = 14.dp)
                        ) {
                            Text(
                                text = "Pengajuan Layanan",
                                fontFamily = PoppinsSemi,
                                fontWeight = FontWeight.Bold,
                                fontSize = 16.sp,
                                color = BlueMain
                            )
                            Spacer(Modifier.height(8.dp))
                            Text(
                                text = "Mohon ditunggu, pengajuan Anda sedang dalam Antrian.",
                                fontFamily = PoppinsReg,
                                fontSize = 13.sp,
                                color = TextPrimary,
                                textAlign = TextAlign.Center,
                                lineHeight = 18.sp
                            )
                        }
                    }

                    Spacer(Modifier.height(16.dp))

                    // Card Detail
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(16.dp),
                        border = BorderStroke(1.dp, BlueBorder),
                        colors = CardDefaults.cardColors(containerColor = Color.White)
                    ) {
                        Column(Modifier.padding(16.dp)) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Image(
                                    painter = painterResource(icDetailHeader),
                                    contentDescription = null,
                                    contentScale = ContentScale.Fit,
                                    modifier = Modifier.size(18.dp)
                                )
                                Spacer(Modifier.width(8.dp))
                                Text(
                                    text = "Detail Pengajuan",
                                    fontFamily = PoppinsSemi,
                                    fontSize = 14.sp,
                                    color = AccentOrange
                                )
                            }

                            Spacer(Modifier.height(12.dp))
                            DetailRow(icon = icNama, title = "Nama", value = nama)
                            DividerLine()
                            DetailRow(icon = icJenis, title = "Jenis Pengajuan", value = jenisPengajuan)
                            DividerLine()
                            DetailRow(icon = icTanggal, title = "Tanggal Pengajuan", value = tanggalPengajuan)
                        }
                    }

                    Spacer(Modifier.height(10.dp))

                    Text(
                        text = "*Jika Anda keluar dari halaman ini, Anda dapat melihat kembali proses pengajuan di halaman Akun",
                        fontFamily = PoppinsReg,
                        fontSize = 10.sp,
                        color = AccentOrange,
                        textAlign = TextAlign.Left,
                        lineHeight = 14.sp,
                        modifier = Modifier.padding(horizontal = 8.dp)
                    )

                    Spacer(Modifier.height(46.dp))

                    OutlinedButton(
                        onClick = { onWhatsappClick?.invoke() },
                        border = BorderStroke(1.dp, BlueMain),
                        shape = RoundedCornerShape(24.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(48.dp)
                    ) {
                        Text(
                            text = "Bantuan Via Whatsapp",
                            fontFamily = PoppinsSemi,
                            fontSize = 14.sp,
                            color = BlueMain
                        )
                    }
                }
            }
        }
    }
}

/* ========= SUBCOMPONENTS ========= */

@Composable
private fun StepItem(
    @DrawableRes icon: Int,
    label: String,
    circleColor: Color
) {
    Column(horizontalAlignment = Alignment.CenterHorizontally) {
        Box(
            modifier = Modifier
                .size(48.dp)
                .clip(CircleShape)
                .background(circleColor),
            contentAlignment = Alignment.Center
        ) {
            Image(
                painter = painterResource(icon),
                contentDescription = null,
                modifier = Modifier.size(24.dp)
            )
        }
        Spacer(Modifier.height(6.dp))
        Text(
            text = label,
            color = Color.White,
            fontSize = 11.sp,
            fontFamily = PoppinsReg,
            textAlign = TextAlign.Center,
            lineHeight = 14.sp
        )
    }
}

/** Garis di tengah pas, bukan offset negatif */
@Composable
private fun RowScope.StepConnector() {
    Box(
        modifier = Modifier
            .weight(1f)
            .height(48.dp), // samakan dengan diameter circle agar posisi terkontrol
        contentAlignment = Alignment.Center
    ) {
        Box(
            modifier = Modifier
                .align(Alignment.Center)
                .offset(y = (-20).dp)  // ⬆️ geser lebih tinggi (tweak di sini kalau mau)
                .fillMaxWidth()
                .height(2.dp)
                .clip(RoundedCornerShape(1.dp))
                .background(BlueLine)
        )
    }
}

@Composable
private fun DividerLine() {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(1.dp)
            .background(BlueBorder)
    )
}

@Composable
private fun DetailRow(
    @DrawableRes icon: Int,
    title: String,
    value: String
) {
    Row(
        verticalAlignment = Alignment.CenterVertically,
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 10.dp)
    ) {
        Image(
            painter = painterResource(icon),
            contentDescription = title,
            modifier = Modifier.size(20.dp)
        )
        Spacer(Modifier.width(10.dp))
        Column {
            Text(
                text = title,
                fontSize = 12.sp,
                fontFamily = PoppinsReg,
                color = TextMuted
            )
            Text(
                text = value,
                fontSize = 14.sp,
                fontFamily = PoppinsSemi,
                color = TextPrimary
            )
        }
    }
}

/* ========= PREVIEW ========= */
@Preview(showSystemUi = true, showBackground = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewProsesPengajuan2() {
    MaterialTheme {
        ProsesPengajuan2Screen()
    }
}
