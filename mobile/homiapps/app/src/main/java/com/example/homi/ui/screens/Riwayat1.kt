package com.example.homi.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.statusBarsPadding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.Description
import androidx.compose.material.icons.filled.LocationOn
import androidx.compose.material.icons.filled.ReportProblem
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.ComplaintDto
import com.example.homi.data.model.ServiceRequestDto
import com.example.homi.data.repository.ComplaintRepository
import com.example.homi.data.repository.ServiceRequestRepository
import kotlinx.coroutines.launch

/* ====== Tokens ====== */
private val BlueMain = Color(0xFF2F7FA3)
private val BlueText = Color(0xFF2F7FA3)
private val LineGray = Color(0xFFE5E7EB)
private val TextDark = Color(0xFF0E0E0E)
private val TextMute = Color(0xFF64748B)
private val BgPage = Color(0xFFF6F7FB)

private val Success = Color(0xFF22C55E)
private val Danger = Color(0xFFEF4444)
private val Warning = Color(0xFFF59E0B)
private val InfoBlue = Color(0xFF2563EB)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

private enum class RiwayatTab { PENGAJUAN, PENGADUAN }

@Composable
fun Riwayat1Screen(
    serviceRepo: ServiceRequestRepository? = null,
    complaintRepo: ComplaintRepository? = null,

    onPengaduanItemClick: ((id: Long) -> Unit)? = null,
    onPengajuanSuratClick: ((id: Long) -> Unit)? = null,

    // preview fallback
    previewPengajuan: List<ServiceRequestDto>? = null,
    previewPengaduan: List<ComplaintDto>? = null,
) {
    var selectedTab by rememberSaveable { mutableStateOf(RiwayatTab.PENGADUAN) }
    val scope = rememberCoroutineScope()

    // ========= PENGAJUAN =========
    var loadingPengajuan by remember { mutableStateOf(true) }
    var errorPengajuan by remember { mutableStateOf<String?>(null) }
    var pengajuanData by remember { mutableStateOf<List<ServiceRequestDto>>(emptyList()) }

    fun loadPengajuan() {
        scope.launch {
            if (serviceRepo == null) {
                pengajuanData = previewPengajuan ?: emptyList()
                loadingPengajuan = false
                errorPengajuan = null
                return@launch
            }

            loadingPengajuan = true
            errorPengajuan = null
            try {
                pengajuanData = serviceRepo.listMy()
            } catch (e: Exception) {
                errorPengajuan = e.message ?: "Gagal memuat riwayat pengajuan"
                pengajuanData = emptyList()
            } finally {
                loadingPengajuan = false
            }
        }
    }

    // ========= PENGADUAN =========
    var loadingPengaduan by remember { mutableStateOf(true) }
    var errorPengaduan by remember { mutableStateOf<String?>(null) }
    var pengaduanData by remember { mutableStateOf<List<ComplaintDto>>(emptyList()) }

    fun loadPengaduan() {
        scope.launch {
            if (complaintRepo == null) {
                pengaduanData = previewPengaduan ?: emptyList()
                loadingPengaduan = false
                errorPengaduan = null
                return@launch
            }

            loadingPengaduan = true
            errorPengaduan = null
            try {
                pengaduanData = complaintRepo.list()
            } catch (e: Exception) {
                errorPengaduan = e.message ?: "Gagal memuat riwayat pengaduan"
                pengaduanData = emptyList()
            } finally {
                loadingPengaduan = false
            }
        }
    }

    LaunchedEffect(serviceRepo) { loadPengajuan() }
    LaunchedEffect(complaintRepo) { loadPengaduan() }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BgPage)
    ) {
        // ===== Header =====
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .background(BlueMain)
                .statusBarsPadding()
                .padding(top = 14.dp, bottom = 18.dp)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Text(
                    text = "Riwayat Layanan",
                    fontFamily = PoppinsSemi,
                    fontSize = 18.sp,
                    color = Color.White
                )
                Spacer(Modifier.height(6.dp))
                Text(
                    text = "Pantau pengajuan dan pengaduan yang pernah Anda buat",
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = Color.White.copy(alpha = 0.95f),
                    textAlign = TextAlign.Center,
                    lineHeight = 18.sp
                )
            }
        }

        // ===== Container putih =====
        Card(
            modifier = Modifier
                .fillMaxSize()
                .offset(y = (-14).dp),
            shape = RoundedCornerShape(topStart = 26.dp, topEnd = 26.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
        ) {
            Spacer(Modifier.height(14.dp))

            // ===== Tabs modern =====
            Card(
                modifier = Modifier
                    .padding(horizontal = 16.dp)
                    .fillMaxWidth(),
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = Color(0xFFF8FAFC)),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(8.dp),
                    horizontalArrangement = Arrangement.spacedBy(10.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    TabPill(
                        selected = selectedTab == RiwayatTab.PENGADUAN,
                        text = "Pengaduan",
                        icon = Icons.Default.ReportProblem,
                        onClick = { selectedTab = RiwayatTab.PENGADUAN }
                    )
                    TabPill(
                        selected = selectedTab == RiwayatTab.PENGAJUAN,
                        text = "Pengajuan",
                        icon = Icons.Default.Description,
                        onClick = { selectedTab = RiwayatTab.PENGAJUAN }
                    )
                }
            }

            Spacer(Modifier.height(12.dp))

            // ===== LIST =====
            when (selectedTab) {
                RiwayatTab.PENGADUAN -> {
                    when {
                        loadingPengaduan -> CenterLoading("Memuat riwayat pengaduan...")
                        errorPengaduan != null -> ErrorBlockModern(
                            message = errorPengaduan!!,
                            onRetry = { loadPengaduan() }
                        )
                        pengaduanData.isEmpty() -> EmptyBlock("Belum ada riwayat pengaduan.")
                        else -> {
                            LazyColumn(
                                contentPadding = PaddingValues(horizontal = 16.dp, vertical = 10.dp),
                                verticalArrangement = Arrangement.spacedBy(12.dp),
                                modifier = Modifier.fillMaxSize()
                            ) {
                                items(pengaduanData) { c ->
                                    val tanggal = formatTanggalForList(c.pickTanggal())
                                    val tempat = c.pickTempat()

                                    RiwayatCardModern(
                                        title = c.perihal.ifBlank { "Pengaduan" },
                                        dateText = tanggal,
                                        placeText = tempat,
                                        statusRaw = c.status.ifBlank { "baru" },
                                        onClick = { onPengaduanItemClick?.invoke(c.id) }
                                    )
                                }
                                item { Spacer(Modifier.height(18.dp)) }
                            }
                        }
                    }
                }

                RiwayatTab.PENGAJUAN -> {
                    when {
                        loadingPengajuan -> CenterLoading("Memuat riwayat pengajuan...")
                        errorPengajuan != null -> ErrorBlockModern(
                            message = errorPengajuan!!,
                            onRetry = { loadPengajuan() }
                        )
                        pengajuanData.isEmpty() -> EmptyBlock("Belum ada riwayat pengajuan.")
                        else -> {
                            LazyColumn(
                                contentPadding = PaddingValues(horizontal = 16.dp, vertical = 10.dp),
                                verticalArrangement = Arrangement.spacedBy(12.dp),
                                modifier = Modifier.fillMaxSize()
                            ) {
                                items(pengajuanData) { sr ->
                                    RiwayatCardModern(
                                        title = sr.subject ?: "Pengajuan Layanan",
                                        dateText = sr.requestDate ?: "-",
                                        placeText = sr.place ?: "-",
                                        statusRaw = sr.status,
                                        onClick = { onPengajuanSuratClick?.invoke(sr.id) }
                                    )
                                }
                                item { Spacer(Modifier.height(18.dp)) }
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun RowScope.TabPill(
    selected: Boolean,
    text: String,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    onClick: () -> Unit
) {
    val bg = if (selected) BlueMain else Color.White
    val fg = if (selected) Color.White else TextDark
    val border = if (selected) BlueMain else LineGray

    Surface(
        modifier = Modifier
            .weight(1f)
            .height(44.dp)
            .clickable { onClick() },
        shape = RoundedCornerShape(14.dp),
        color = bg,
        tonalElevation = 0.dp,
        shadowElevation = 0.dp
    ) {
        Box(
            modifier = Modifier
                .fillMaxSize()
                .border(1.dp, border, RoundedCornerShape(14.dp))
                .padding(horizontal = 12.dp),
            contentAlignment = Alignment.Center
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(icon, contentDescription = null, tint = fg, modifier = Modifier.size(18.dp))
                Spacer(Modifier.width(8.dp))
                Text(text, fontFamily = PoppinsSemi, fontSize = 13.sp, color = fg)
            }
        }
    }
}

@Composable
private fun RiwayatCardModern(
    title: String,
    dateText: String,
    placeText: String,
    statusRaw: String,
    onClick: () -> Unit
) {
    val (label, color) = mapStatus(statusRaw)

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, LineGray, RoundedCornerShape(16.dp))
        ) {
            Box(
                modifier = Modifier
                    .width(8.dp)
                    .fillMaxHeight()
                    .background(color, RoundedCornerShape(topStart = 16.dp, bottomStart = 16.dp))
            )

            Column(
                modifier = Modifier
                    .padding(horizontal = 14.dp, vertical = 12.dp)
                    .fillMaxWidth(),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text(
                        text = title,
                        fontFamily = PoppinsSemi,
                        fontSize = 14.sp,
                        color = TextDark,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis,
                        modifier = Modifier.weight(1f)
                    )
                    StatusChipSmall(label = label, color = color)
                }

                InfoRow(icon = Icons.Default.CalendarMonth, text = "Tanggal: ${dateText.ifBlank { "-" }}")
                InfoRow(icon = Icons.Default.LocationOn, text = "Tempat: ${placeText.ifBlank { "-" }}")

                Text(
                    text = "Ketuk untuk melihat detail/progres",
                    fontFamily = PoppinsReg,
                    fontSize = 11.sp,
                    color = BlueText
                )
            }
        }
    }
}

@Composable
private fun InfoRow(icon: androidx.compose.ui.graphics.vector.ImageVector, text: String) {
    Row(verticalAlignment = Alignment.CenterVertically) {
        Icon(icon, contentDescription = null, tint = TextMute, modifier = Modifier.size(16.dp))
        Spacer(Modifier.width(8.dp))
        Text(
            text = text,
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = TextMute,
            maxLines = 1,
            overflow = TextOverflow.Ellipsis
        )
    }
}

@Composable
private fun StatusChipSmall(label: String, color: Color) {
    Box(
        modifier = Modifier
            .background(color.copy(alpha = 0.12f), RoundedCornerShape(999.dp))
            .border(1.dp, color.copy(alpha = 0.30f), RoundedCornerShape(999.dp))
            .padding(horizontal = 10.dp, vertical = 5.dp)
    ) {
        Text(text = label, fontFamily = PoppinsSemi, fontSize = 11.sp, color = color)
    }
}

@Composable
private fun CenterLoading(text: String) {
    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            CircularProgressIndicator(strokeWidth = 2.dp)
            Spacer(Modifier.height(10.dp))
            Text(text, fontFamily = PoppinsReg, fontSize = 12.sp, color = TextMute)
        }
    }
}

@Composable
private fun EmptyBlock(text: String) {
    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Text(text, fontFamily = PoppinsReg, fontSize = 12.sp, color = TextMute)
    }
}

@Composable
private fun ErrorBlockModern(message: String, onRetry: () -> Unit) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(18.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Text(
            text = message,
            fontFamily = PoppinsReg,
            color = Color(0xFFDC2626),
            textAlign = TextAlign.Center
        )
        Spacer(Modifier.height(10.dp))
        OutlinedButton(
            onClick = onRetry,
            border = androidx.compose.foundation.BorderStroke(1.dp, BlueText),
            shape = RoundedCornerShape(14.dp)
        ) {
            Text("Coba Lagi", fontFamily = PoppinsSemi, color = BlueText)
        }
    }
}

private fun mapStatus(raw: String): Pair<String, Color> {
    val s = raw.trim().lowercase()
    return when {
        s.contains("tolak") || s.contains("rejected") || s.contains("ditolak") -> "Ditolak" to Danger
        s.contains("selesai") || s.contains("done") || s.contains("approved") || s.contains("paid") -> "Selesai" to Success
        s.contains("baru") || s.contains("pending") || s.contains("submitted") -> "Baru" to InfoBlue
        else -> "Diproses" to Warning
    }
}

/* ========= HELPERS khusus ComplaintDto ========= */
private fun ComplaintDto.pickTempat(): String {
    val a = this.tempat?.trim().orEmpty()
    if (a.isNotBlank()) return a

    val b = this.tempatKejadian.trim()
    if (b.isNotBlank()) return b

    return "-"
}

private fun ComplaintDto.pickTanggal(): String {
    val label = this.tanggalLabel?.trim().orEmpty()
    if (label.isNotBlank()) return label

    val legacy = this.tanggalPengaduan.trim()
    if (legacy.isNotBlank()) return legacy

    val iso = (this.tanggalIso?.trim().orEmpty())
    if (iso.isNotBlank()) return iso

    val created = (this.createdAt?.trim().orEmpty())
    if (created.isNotBlank()) return created

    return "-"
}

private fun formatTanggalForList(raw: String): String {
    val t = raw.trim()
    if (t.isBlank() || t == "-") return "-"

    // kalau format "9 Januari 2026" (ada huruf), tampilkan apa adanya
    if (t.any { it.isLetter() }) return t

    // kalau ISO timestamp "2026-01-09T..." ambil 10 char dulu
    val d10 = if (t.length >= 10) t.substring(0, 10) else t
    if (d10.length == 10 && d10[4] == '-' && d10[7] == '-') {
        val y = d10.substring(0, 4)
        val m = d10.substring(5, 7)
        val d = d10.substring(8, 10)
        return "$d/$m/$y"
    }
    return t
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewRiwayatModern() {
    MaterialTheme {
        Riwayat1Screen(
            serviceRepo = null,
            complaintRepo = null,
            previewPengajuan = emptyList(),
            previewPengaduan = emptyList()
        )
    }
}
