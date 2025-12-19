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
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
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
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.withStyle
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.delay

/* ========= THEME ========= */
private val BlueMain = Color(0xFF2F79A0)
private val BlueLine = Color(0xFFFFFFFF)
private val BlueBorder = Color(0xFF2F79A0)
private val AccentOrange = Color(0xFFFF9966)
private val TextPrimary = Color(0xFF0E0E0E)
private val TextMuted = Color(0xFF8A8A8A)
private val DangerRed = Color(0xFFE53935)
private val SuccessGreen = Color(0xFF22C55E)
private val InfoBlue = Color(0xFF3B82F6)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

/* ========= STATE ========= */
enum class ProsesPengaduanState { ANTRIAN, DIPROSES, SELESAI }
enum class HasilPengaduan { NONE, DITOLAK, DITERIMA, DISELIDIKI }

/**
 * Wrapper untuk dipanggil dari NavHost
 */
@Composable
fun PengaduanWargaScreen(
    onBack: (() -> Unit)? = null,
    onWhatsappClick: (() -> Unit)? = null,
) {
    ProsesPengaduanScreen(
        onBack = onBack,
        onWhatsappClick = onWhatsappClick
    )
}

/**
 * Stepper proses pengaduan
 */
@Composable
fun ProsesPengaduanScreen(
    state: ProsesPengaduanState = ProsesPengaduanState.ANTRIAN,

    nomorPengaduan: String = "001",
    nama: String = "Lily",
    jenisPengaduan: String = "Surat Keterangan",
    tanggalPengaduan: String = "01 Oktober 2021",

    hasil: HasilPengaduan = HasilPengaduan.DITOLAK,
    catatanStatus: String = "Peminjaman Fasilitas pada tanggal 03 September 2025 telah penuh.",

    onBack: (() -> Unit)? = null,
    onWhatsappClick: (() -> Unit)? = null,

    demoAutoFlow: Boolean = false,
    demoStepDelayMs: Long = 1500L,
    demoLoop: Boolean = false,

    @DrawableRes icBack: Int = R.drawable.panahkembali,

    @DrawableRes icStepPengaduan1: Int = R.drawable.ic_pengajuan_aktif,
    @DrawableRes icStepPengaduan2: Int = R.drawable.ic_pengajuan_aktif2,
    @DrawableRes icStepProses1: Int = R.drawable.ic_proses,
    @DrawableRes icStepProses2: Int = R.drawable.ic_proses2,
    @DrawableRes icStepSelesai1: Int = R.drawable.ic_selesai,
    @DrawableRes icStepSelesai2: Int = R.drawable.ic_selesai2,

    @DrawableRes icDetailHeader: Int = R.drawable.ic_detail_header,
    @DrawableRes icNama: Int = R.drawable.ic_user,
    @DrawableRes icJenis: Int = R.drawable.ic_doc,
    @DrawableRes icTanggal: Int = R.drawable.ic_calendar
) {
    var demoState by rememberSaveable { mutableStateOf(state) }
    val currentState = if (demoAutoFlow) demoState else state

    LaunchedEffect(demoAutoFlow, demoLoop, demoStepDelayMs) {
        if (!demoAutoFlow) return@LaunchedEffect

        demoState = ProsesPengaduanState.ANTRIAN
        delay(demoStepDelayMs)

        demoState = ProsesPengaduanState.DIPROSES
        delay(demoStepDelayMs)

        demoState = ProsesPengaduanState.SELESAI
        delay(demoStepDelayMs)

        if (demoLoop) {
            while (true) {
                demoState = ProsesPengaduanState.ANTRIAN
                delay(demoStepDelayMs)

                demoState = ProsesPengaduanState.DIPROSES
                delay(demoStepDelayMs)

                demoState = ProsesPengaduanState.SELESAI
                delay(demoStepDelayMs)
            }
        }
    }

    val step1Circle = when (currentState) {
        ProsesPengaduanState.ANTRIAN -> Color(0xFFF7A477)
        ProsesPengaduanState.DIPROSES, ProsesPengaduanState.SELESAI -> Color.White
    }
    val step2Circle = when (currentState) {
        ProsesPengaduanState.ANTRIAN -> Color.White
        ProsesPengaduanState.DIPROSES -> AccentOrange
        ProsesPengaduanState.SELESAI -> Color.White
    }
    val step3Circle = when (currentState) {
        ProsesPengaduanState.ANTRIAN, ProsesPengaduanState.DIPROSES -> Color.White
        ProsesPengaduanState.SELESAI -> AccentOrange
    }

    val step1Icon = if (currentState == ProsesPengaduanState.ANTRIAN) icStepPengaduan1 else icStepPengaduan2
    val step2Icon = if (currentState == ProsesPengaduanState.DIPROSES) icStepProses2 else icStepProses1
    val step3Icon = if (currentState == ProsesPengaduanState.SELESAI) icStepSelesai2 else icStepSelesai1

    val statusTitle = "Pengaduan Layanan"
    val statusMessage = when (currentState) {
        ProsesPengaduanState.ANTRIAN,
        ProsesPengaduanState.DIPROSES -> "Mohon ditunggu, pengaduan Anda sedang dalam Antrian."
        ProsesPengaduanState.SELESAI -> "Pengaduan selesai."
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
    ) {
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
            Text(
                text = "Pengaduan Layanan",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.width(24.dp))
        }

        Spacer(Modifier.height(8.dp))

        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            StepItem(icon = step1Icon, label = "Pengaduan\nLayanan", circleColor = step1Circle)
            StepConnector()
            StepItem(icon = step2Icon, label = "Sedang\nDiproses", circleColor = step2Circle)
            StepConnector()
            StepItem(icon = step3Icon, label = "Pengaduan\nSelesai", circleColor = step3Circle)
        }

        Spacer(Modifier.height(18.dp))

        Column(
            modifier = Modifier.fillMaxWidth(),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(
                text = "Nomor Pengaduan",
                fontFamily = PoppinsReg,
                fontSize = 14.sp,
                color = Color.White
            )
            Text(
                text = nomorPengaduan,
                fontFamily = PoppinsSemi,
                fontWeight = FontWeight.Bold,
                fontSize = 32.sp,
                color = Color.White
            )
        }

        Spacer(Modifier.height(18.dp))

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
                            text = statusTitle,
                            fontFamily = PoppinsSemi,
                            fontWeight = FontWeight.Bold,
                            fontSize = 16.sp,
                            color = BlueMain,
                            modifier = Modifier.fillMaxWidth(),
                            textAlign = TextAlign.Center
                        )
                        Spacer(Modifier.height(8.dp))
                        Text(
                            text = statusMessage,
                            fontFamily = PoppinsReg,
                            fontSize = 13.sp,
                            color = TextPrimary,
                            textAlign = TextAlign.Center,
                            lineHeight = 18.sp,
                            modifier = Modifier.fillMaxWidth()
                        )
                    }
                }

                Spacer(Modifier.height(16.dp))

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
                                text = "Detail Pengaduan",
                                fontFamily = PoppinsSemi,
                                fontSize = 14.sp,
                                color = AccentOrange
                            )
                        }

                        Spacer(Modifier.height(12.dp))
                        DetailRow(icon = icNama, title = "Nama", value = nama)
                        DividerLine()
                        DetailRow(icon = icJenis, title = "Jenis Pengaduan", value = jenisPengaduan)
                        DividerLine()
                        DetailRow(icon = icTanggal, title = "Tanggal Pengaduan", value = tanggalPengaduan)

                        if (currentState == ProsesPengaduanState.SELESAI && hasil != HasilPengaduan.NONE) {
                            DividerLine()
                            Spacer(Modifier.height(8.dp))

                            Text(
                                text = "Status",
                                fontFamily = PoppinsSemi,
                                fontSize = 13.sp,
                                color = AccentOrange,
                                modifier = Modifier.fillMaxWidth(),
                                textAlign = TextAlign.Center
                            )

                            Spacer(Modifier.height(6.dp))

                            val (kata, warna) = when (hasil) {
                                HasilPengaduan.DITOLAK -> "Tolak" to DangerRed
                                HasilPengaduan.DITERIMA -> "Terima" to SuccessGreen
                                HasilPengaduan.DISELIDIKI -> "Selidiki" to InfoBlue
                                HasilPengaduan.NONE -> "" to TextPrimary
                            }

                            val statusText = buildAnnotatedString {
                                append("Pengaduan Anda di ")
                                withStyle(
                                    SpanStyle(
                                        color = warna,
                                        fontFamily = PoppinsSemi,
                                        fontWeight = FontWeight.SemiBold
                                    )
                                ) { append(kata) }
                                if (catatanStatus.isNotBlank()) {
                                    append(", ")
                                    append(catatanStatus)
                                }
                            }

                            Text(
                                text = statusText,
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
                }

                Spacer(Modifier.height(10.dp))

                if (currentState != ProsesPengaduanState.SELESAI) {
                    Text(
                        text = "*Jika Anda keluar dari halaman ini, Anda dapat melihat kembali proses pengaduan di halaman Akun",
                        fontFamily = PoppinsReg,
                        fontSize = 10.sp,
                        color = AccentOrange,
                        textAlign = TextAlign.Left,
                        lineHeight = 14.sp,
                        modifier = Modifier.padding(horizontal = 8.dp)
                    )
                }

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

/** garis dinaikkan seperti yang kamu pakai */
@Composable
private fun RowScope.StepConnector() {
    Box(
        modifier = Modifier
            .weight(1f)
            .height(48.dp),
        contentAlignment = Alignment.Center
    ) {
        Box(
            modifier = Modifier
                .align(Alignment.Center)
                .offset(y = (-20).dp)
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
            .background(BlueBorder.copy(alpha = 0.6f))
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

/* ========= PREVIEWS ========= */
@Preview(showSystemUi = true, showBackground = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun Preview_DemoAutoFlow() {
    MaterialTheme {
        ProsesPengaduanScreen(
            demoAutoFlow = true,
            demoStepDelayMs = 900L,
            demoLoop = true
        )
    }
}

@Preview(showSystemUi = true, showBackground = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun Preview_Selesai_Ditolak() {
    MaterialTheme {
        ProsesPengaduanScreen(
            state = ProsesPengaduanState.SELESAI,
            hasil = HasilPengaduan.DITOLAK,
            catatanStatus = "Peminjaman Fasilitas pada tanggal 03 September 2025 telah penuh."
        )
    }
}
