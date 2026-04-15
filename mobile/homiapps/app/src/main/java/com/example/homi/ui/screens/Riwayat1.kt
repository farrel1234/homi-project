package com.example.homi.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.statusBarsPadding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
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

/* ====== Modern Tokens ====== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueDark     = Color(0xFF1A5E7B)
private val BlueText     = Color(0xFF2F7FA3)
private val BgPage       = Color(0xFFF8FAFC)
private val LineGray     = Color(0xFFE2E8F0)
private val TextPrimary  = Color(0xFF0F172A)
private val TextMuted    = Color(0xFF64748B)
private val AccentOrange = Color(0xFFF7A477)

private val Success      = Color(0xFF22C55E)
private val Danger       = Color(0xFFEF4444)
private val Warning      = Color(0xFFF59E0B)
private val InfoBlue     = Color(0xFF3B82F6)

private val PoppinsSemi  = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg   = FontFamily(Font(R.font.poppins_regular))

private enum class RiwayatTab { PENGAJUAN, PENGADUAN }

@Composable
fun Riwayat1Screen(
    serviceRepo: ServiceRequestRepository? = null,
    complaintRepo: ComplaintRepository? = null,

    onPengaduanItemClick: ((id: Long) -> Unit)? = null,
    onPengajuanSuratClick: ((id: Long) -> Unit)? = null,

    previewPengajuan: List<ServiceRequestDto>? = null,
    previewPengaduan: List<ComplaintDto>? = null,
) {
    var selectedTab by rememberSaveable { mutableStateOf(RiwayatTab.PENGADUAN) }
    val scope = rememberCoroutineScope()

    // ========= DATA STATES =========
    var loadingPengajuan by remember { mutableStateOf(true) }
    var errorPengajuan by remember { mutableStateOf<String?>(null) }
    var pengajuanData by remember { mutableStateOf<List<ServiceRequestDto>>(emptyList()) }

    var loadingPengaduan by remember { mutableStateOf(true) }
    var errorPengaduan by remember { mutableStateOf<String?>(null) }
    var pengaduanData by remember { mutableStateOf<List<ComplaintDto>>(emptyList()) }

    fun loadPengajuan() {
        scope.launch {
            if (serviceRepo == null) {
                pengajuanData = previewPengajuan ?: emptyList()
                loadingPengajuan = false; errorPengajuan = null
                return@launch
            }
            loadingPengajuan = true; errorPengajuan = null
            try { pengajuanData = serviceRepo.listMy() }
            catch (e: Exception) { errorPengajuan = e.message ?: "Gagal memuat pengajuan" }
            finally { loadingPengajuan = false }
        }
    }

    fun loadPengaduan() {
        scope.launch {
            if (complaintRepo == null) {
                pengaduanData = previewPengaduan ?: emptyList()
                loadingPengaduan = false; errorPengaduan = null
                return@launch
            }
            loadingPengaduan = true; errorPengaduan = null
            try { pengaduanData = complaintRepo.list() }
            catch (e: Exception) { errorPengaduan = e.message ?: "Gagal memuat pengaduan" }
            finally { loadingPengaduan = false }
        }
    }

    LaunchedEffect(serviceRepo) { loadPengajuan() }
    LaunchedEffect(complaintRepo) { loadPengaduan() }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Brush.verticalGradient(listOf(BlueMain, BlueDark)))
    ) {
        // ===== PREMIUM GRADIENT HEADER =====
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .statusBarsPadding()
                .padding(top = 28.dp, bottom = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Box(
                modifier = Modifier
                    .size(54.dp)
                    .background(Color.White.copy(alpha = 0.15f), RoundedCornerShape(16.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Default.History,
                    contentDescription = null,
                    tint = Color.White,
                    modifier = Modifier.size(28.dp)
                )
            }
            
            Spacer(Modifier.height(16.dp))
            
            Text(
                text = "Riwayat Aktivitas",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White
            )
            
            Spacer(Modifier.height(4.dp))
            
            Text(
                text = "Pantau perkembangan pengaduan & pengajuan Anda.",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center
            )
        }

        // ===== CONTENT CONTAINER =====
        Surface(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            color = BgPage
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(top = 16.dp)
            ) {
                // ===== TABS SEGMENTED =====
                Card(
                    modifier = Modifier
                        .padding(horizontal = 20.dp)
                        .fillMaxWidth(),
                    shape = RoundedCornerShape(20.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(6.dp),
                        horizontalArrangement = Arrangement.spacedBy(8.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        TabPill(
                            selected = selectedTab == RiwayatTab.PENGADUAN,
                            text = "Pengaduan",
                            icon = Icons.Default.Campaign,
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

                // ===== LIST VIEW =====
                Box(modifier = Modifier.fillMaxSize()) {
                    when (selectedTab) {
                        RiwayatTab.PENGADUAN -> {
                            when {
                                loadingPengaduan -> CenterLoading("Mendapatkan data...")
                                errorPengaduan != null -> ErrorBlockView(errorPengaduan!!) { loadPengaduan() }
                                pengaduanData.isEmpty() -> EmptyBlockView("Belum ada pengaduan.")
                                else -> {
                                    LazyColumn(
                                        contentPadding = PaddingValues(horizontal = 20.dp, vertical = 8.dp),
                                        verticalArrangement = Arrangement.spacedBy(14.dp)
                                    ) {
                                        items(pengaduanData) { c ->
                                            RiwayatCardModern(
                                                title = c.perihal.ifBlank { "Pengaduan Warga" },
                                                dateText = formatTanggalForList(c.pickTanggal()),
                                                placeText = c.pickTempat(),
                                                statusRaw = c.status.ifBlank { "baru" },
                                                categoryIcon = Icons.Default.ReportProblem,
                                                onClick = { onPengaduanItemClick?.invoke(c.id) }
                                            )
                                        }
                                        item { Spacer(Modifier.height(80.dp)) }
                                    }
                                }
                            }
                        }

                        RiwayatTab.PENGAJUAN -> {
                            when {
                                loadingPengajuan -> CenterLoading("Mendapatkan data...")
                                errorPengajuan != null -> ErrorBlockView(errorPengajuan!!) { loadPengajuan() }
                                pengajuanData.isEmpty() -> EmptyBlockView("Belum ada pengajuan.")
                                else -> {
                                    LazyColumn(
                                        contentPadding = PaddingValues(horizontal = 20.dp, vertical = 8.dp),
                                        verticalArrangement = Arrangement.spacedBy(14.dp)
                                    ) {
                                        items(pengajuanData) { sr ->
                                            RiwayatCardModern(
                                                title = sr.subject ?: "Pengajuan Layanan",
                                                dateText = formatTanggalForList(sr.requestDate ?: "-"),
                                                placeText = sr.place ?: "-",
                                                statusRaw = sr.status,
                                                categoryIcon = Icons.Default.Assignment,
                                                onClick = { onPengajuanSuratClick?.invoke(sr.id) }
                                            )
                                        }
                                        item { Spacer(Modifier.height(80.dp)) }
                                    }
                                }
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
    icon: ImageVector,
    onClick: () -> Unit
) {
    val bg = if (selected) BlueMain else Color.Transparent
    val fg = if (selected) Color.White else TextMuted
    
    Surface(
        modifier = Modifier
            .weight(1f)
            .height(46.dp)
            .clickable { onClick() },
        shape = RoundedCornerShape(14.dp),
        color = bg,
    ) {
        Row(
            modifier = Modifier.fillMaxSize(),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.Center
        ) {
            Icon(icon, contentDescription = null, tint = fg, modifier = Modifier.size(18.dp))
            Spacer(Modifier.width(8.dp))
            Text(text, fontFamily = PoppinsSemi, fontSize = 13.sp, color = fg)
        }
    }
}

@Composable
private fun RiwayatCardModern(
    title: String,
    dateText: String,
    placeText: String,
    statusRaw: String,
    categoryIcon: ImageVector,
    onClick: () -> Unit
) {
    val (label, statusColor) = mapStatus(statusRaw)

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        shape = RoundedCornerShape(20.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.5.dp),
        border = androidx.compose.foundation.BorderStroke(1.dp, LineGray.copy(alpha = 0.5f))
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(14.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Category Icon Box
            Box(
                modifier = Modifier
                    .size(52.dp)
                    .background(statusColor.copy(alpha = 0.12f), CircleShape),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = categoryIcon,
                    contentDescription = null,
                    tint = statusColor,
                    modifier = Modifier.size(24.dp)
                )
            }

            Spacer(Modifier.width(16.dp))

            Column(modifier = Modifier.weight(1f)) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text(
                        text = title,
                        fontFamily = PoppinsSemi,
                        fontSize = 15.sp,
                        color = TextPrimary,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis,
                        modifier = Modifier.weight(1f)
                    )
                    StatusChip(label, statusColor)
                }
                
                Spacer(Modifier.height(6.dp))

                InfoRow(icon = Icons.Default.CalendarMonth, text = dateText)
                InfoRow(icon = Icons.Default.LocationOn, text = placeText)
            }
            
            Spacer(Modifier.width(8.dp))
            
            Icon(
                imageVector = Icons.Default.ChevronRight,
                contentDescription = null,
                tint = LineGray,
                modifier = Modifier.size(20.dp)
            )
        }
    }
}

@Composable
private fun InfoRow(icon: ImageVector, text: String) {
    Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.padding(vertical = 1.dp)) {
        Icon(icon, contentDescription = null, tint = TextMuted, modifier = Modifier.size(13.dp))
        Spacer(Modifier.width(6.dp))
        Text(
            text = text,
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = TextMuted,
            maxLines = 1,
            overflow = TextOverflow.Ellipsis
        )
    }
}

@Composable
private fun StatusChip(label: String, color: Color) {
    Surface(
        color = color.copy(alpha = 0.12f),
        shape = RoundedCornerShape(8.dp),
        border = androidx.compose.foundation.BorderStroke(1.dp, color.copy(alpha = 0.25f))
    ) {
        Text(
            text = label,
            fontFamily = PoppinsSemi,
            fontSize = 10.sp,
            color = color,
            modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
        )
    }
}

@Composable
private fun CenterLoading(text: String) {
    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            CircularProgressIndicator(color = BlueMain, strokeWidth = 3.dp, modifier = Modifier.size(36.dp))
            Spacer(Modifier.height(12.dp))
            Text(text, fontFamily = PoppinsReg, fontSize = 13.sp, color = TextMuted)
        }
    }
}

@Composable
private fun EmptyBlockView(text: String) {
    Box(Modifier.fillMaxSize().padding(32.dp), contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            Icon(Icons.Default.Inventory2, contentDescription = null, tint = LineGray.copy(alpha = 0.6f), modifier = Modifier.size(72.dp))
            Spacer(Modifier.height(12.dp))
            Text(text, fontFamily = PoppinsSemi, fontSize = 15.sp, color = TextMuted, textAlign = TextAlign.Center)
        }
    }
}

@Composable
private fun ErrorBlockView(message: String, onRetry: () -> Unit) {
    Column(
        Modifier.fillMaxSize().padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Icon(Icons.Default.ErrorOutline, contentDescription = null, tint = Danger, modifier = Modifier.size(48.dp))
        Spacer(Modifier.height(16.dp))
        Text(message, fontFamily = PoppinsReg, color = Danger, textAlign = TextAlign.Center)
        Spacer(Modifier.height(20.dp))
        Button(
            onClick = onRetry,
            colors = ButtonDefaults.buttonColors(containerColor = BlueMain),
            shape = RoundedCornerShape(12.dp)
        ) {
            Text("Coba Lagi", fontFamily = PoppinsSemi)
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

private fun ComplaintDto.pickTempat(): String {
    val a = this.tempat?.trim().orEmpty()
    if (a.isNotBlank()) return a
    val b = this.tempatKejadian.trim()
    return if (b.isNotBlank()) b else "-"
}

private fun ComplaintDto.pickTanggal(): String {
    val label = this.tanggalLabel?.trim().orEmpty()
    if (label.isNotBlank()) return label
    val legacy = this.tanggalPengaduan.trim()
    if (legacy.isNotBlank()) return legacy
    val iso = (this.tanggalIso?.trim().orEmpty())
    if (iso.isNotBlank()) return iso
    val created = (this.createdAt?.trim().orEmpty())
    return if (created.isNotBlank()) created else "-"
}

private fun formatTanggalForList(raw: String): String {
    val t = raw.trim()
    if (t.isBlank() || t == "-") return "-"
    if (t.contains(Regex("[a-zA-Z]")) && !t.contains("T")) return t
    if (t.length >= 10 && t[4] == '-' && t[7] == '-') {
        return try {
            val y = t.substring(0, 4)
            val m = t.substring(5, 7)
            val d = t.substring(8, 10).toInt().toString()
            val bln = when (m) {
                "01" -> "Januari" ; "02" -> "Februari" ; "03" -> "Maret" ; "04" -> "April"
                "05" -> "Mei" ; "06" -> "Juni" ; "07" -> "Juli" ; "08" -> "Agustus"
                "09" -> "September" ; "10" -> "Oktober" ; "11" -> "November" ; "12" -> "Desember"
                else -> m
            }
            "$d $bln $y"
        } catch (e: Exception) { t }
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
