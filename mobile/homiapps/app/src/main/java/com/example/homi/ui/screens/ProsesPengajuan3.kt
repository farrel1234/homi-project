package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.RowScope
import androidx.compose.foundation.layout.statusBarsPadding
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.SpanStyle
import androidx.compose.ui.text.buildAnnotatedString
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.withStyle
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import androidx.compose.ui.text.style.TextAlign

private val BlueMain   = Color(0xFF2F7FA3)
private val BlueLine   = Color(0xFFFFFFFF)
private val BlueBorder = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val TextPrimary = Color(0xFF0E0E0E)
private val TextMuted   = Color(0xFF8A8A8A)
private val DangerRed   = Color(0xFFE53935)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun ProsesPengajuan3(
    nomorPengajuan: String = "001",
    nama: String = "Lily",
    jenisPengajuan: String = "Surat Keterangan",
    tanggalPengajuan: String = "01 Oktober 2021",
    onBack: (() -> Unit)? = null,
    onWhatsappClick: (() -> Unit)? = null,
    @DrawableRes icBack: Int = R.drawable.ic_launcher_foreground,  // ganti ke panahmu jika ada
    @DrawableRes icStepPengajuan: Int = R.drawable.ic_pengajuan_aktif2,
    @DrawableRes icStepProses: Int = R.drawable.ic_proses,
    @DrawableRes icStepSelesai: Int = R.drawable.ic_selesai2,
    @DrawableRes icDetailHeader: Int = R.drawable.ic_detail_header,
    @DrawableRes icNama: Int = R.drawable.ic_user,
    @DrawableRes icJenis: Int = R.drawable.ic_doc,
    @DrawableRes icTanggal: Int = R.drawable.ic_calendar,
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
    ) {

        Row(
            modifier = Modifier
                .fillMaxWidth()
                .statusBarsPadding()
                .padding(horizontal = 16.dp, vertical = 12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Image(
                painter = painterResource(R.drawable.panahkembali),
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
                modifier = Modifier.fillMaxWidth(),
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.width(24.dp))
        }

        Spacer(Modifier.height(8.dp))
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 36.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            StepItem(icon = icStepPengajuan, circleColor = Color.White)
            StepConnector()
            StepItem(icon = icStepProses, circleColor = Color.White)
            StepConnector()
            StepItem(icon = icStepSelesai, circleColor = AccentOrange)
        }

        Spacer(Modifier.height(8.dp))
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp),
            horizontalArrangement = Arrangement.SpaceAround
        ) {
            StepLabel("Pengajuan\nLayanan")
            StepLabel("Sedang\nDiproses")
            StepLabel("Pengajuan\nSelesai")
        }

        Spacer(Modifier.height(12.dp))
        Column(
            modifier = Modifier.fillMaxWidth(),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text("Nomor Pengajuan", fontFamily = PoppinsReg, fontSize = 14.sp, color = Color.White)
            Text(nomorPengajuan, fontFamily = PoppinsSemi, fontSize = 30.sp, color = Color.White)
        }


        Spacer(Modifier.height(16.dp))
        Card(
            modifier = Modifier
                .fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 16.dp, vertical = 18.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {

                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(16.dp),
                    border = BorderStroke(2.dp, BlueBorder),
                    colors = CardDefaults.cardColors(containerColor = Color.White)
                ) {
                    Column(
                        modifier = Modifier.padding(16.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        Text(
                            "Pengajuan Layanan",
                            fontFamily = PoppinsSemi,
                            fontSize = 16.sp,
                            color = BlueMain,
                            modifier = Modifier.fillMaxWidth(),
                            textAlign = TextAlign.Center
                        )
                        Spacer(Modifier.height(6.dp))
                        Text(
                            "Pengajuan selesai.",
                            fontFamily = PoppinsReg,
                            fontSize = 13.sp,
                            color = TextPrimary,
                            modifier = Modifier.fillMaxWidth(),
                            textAlign = TextAlign.Center
                        )
                    }
                }

                Spacer(Modifier.height(12.dp))

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
                                "Detail Pengajuan",
                                fontFamily = PoppinsSemi,
                                fontSize = 14.sp,
                                color = AccentOrange
                            )
                        }

                        Spacer(Modifier.height(12.dp))
                        DetailRow(icNama, "Nama", nama)
                        DividerLine()
                        DetailRow(icJenis, "Jenis Pengajuan", jenisPengajuan, boldValue = true)
                        DividerLine()
                        DetailRow(icTanggal, "Tanggal Pengajuan", tanggalPengajuan, boldValue = true)
                        DividerLine()

                        Spacer(Modifier.height(8.dp))
                        Text(
                            "Status",
                            fontFamily = PoppinsSemi,
                            fontSize = 13.sp,
                            color = AccentOrange,
                            modifier = Modifier.fillMaxWidth(),
                            textAlign = TextAlign.Center
                        )
                        Spacer(Modifier.height(4.dp))
                        val status = buildAnnotatedString {
                            append("Pengajuan Anda di ")
                            withStyle(SpanStyle(color = DangerRed, fontFamily = PoppinsSemi)) {
                                append("Tolak")
                            }
                            append(", Peminjaman Fasilitas pada tanggal 03 September 2025 telah penuh.")
                        }
                        Text(
                            status,
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = TextPrimary,
                            lineHeight = 18.sp,
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(horizontal = 8.dp),
                            textAlign = TextAlign.Center
                        )
                    }
                }


                Spacer(Modifier.height(18.dp))
                OutlinedButton(
                    onClick = { onWhatsappClick?.invoke() },
                    shape = RoundedCornerShape(24.dp),
                    border = BorderStroke(1.dp, BlueBorder),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp)
                ) {
                    Text("Bantuan Via Whatsapp", fontFamily = PoppinsSemi, fontSize = 14.sp, color = BlueMain)
                }
            }
        }
    }
}

@Composable
private fun StepItem(
    @DrawableRes icon: Int,
    circleColor: Color
) {
    Box(
        modifier = Modifier
            .size(56.dp)
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
}

@Composable
private fun RowScope.StepConnector() {
    Box(
        modifier = Modifier
            .weight(1f)
            .height(56.dp),
        contentAlignment = Alignment.Center
    ) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(3.dp)
                .clip(RoundedCornerShape(2.dp))
                .background(BlueLine)
        )
    }
}

@Composable
private fun StepLabel(text: String) {
    Text(
        text = text,
        color = Color.White,
        fontFamily = PoppinsReg,
        fontSize = 12.sp,
        lineHeight = 16.sp,
        textAlign = androidx.compose.ui.text.style.TextAlign.Center
    )
}

@Composable
private fun DividerLine() {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(1.dp)
            .background(BlueBorder.copy(alpha = 0.6f))
    )
}

@Composable
private fun DetailRow(
    @DrawableRes icon: Int,
    title: String,
    value: String,
    boldValue: Boolean = false
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
        Column(Modifier.fillMaxWidth()) {
            Text(title, fontFamily = PoppinsReg, fontSize = 12.sp, color = TextMuted)
            Text(
                value,
                fontFamily = if (boldValue) PoppinsSemi else PoppinsReg,
                fontSize = 14.sp,
                color = TextPrimary
            )
        }
    }
}

@Preview(showSystemUi = true, showBackground = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewProsesPengajuan3() {
    MaterialTheme { ProsesPengajuan3() }
}
