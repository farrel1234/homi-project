// File: TagihanIuranScreen.kt
package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.*
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AccountBalanceWallet
import androidx.compose.material.icons.filled.ArrowForwardIos
import androidx.compose.material.icons.filled.Info
import androidx.compose.material.icons.filled.Payments
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.FeeInvoiceDto
import com.example.homi.data.repository.FeeRepository
import kotlinx.coroutines.launch

/* ===== UI TOKENS (Match Premium Theme) ===== */
private val BlueMain = Color(0xFF2F7FA3)
private val BlueDark = Color(0xFF1E5570)
private val BlueSurface = Color(0xFFF1F5F9)
private val AccentOrange = Color(0xFFE26A2C)
private val SuccessGreen = Color(0xFF22C55E)
private val PendingBlue = Color(0xFF2563EB)
private val TextDark = Color(0xFF1E293B)
private val TextMuted = Color(0xFF64748B)
private val BorderColor = Color(0xFFE2E8F0)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

/* ===== DATA CLASSES ===== */
data class TagihanItem(
    val invoiceId: Long,
    val trxId: String,
    val bulan: String,
    val nominal: String,
    val rawNominal: Long,
    val status: String
)

data class TagihanTahun(val label: String, val items: List<TagihanItem>)

@Composable
fun TagihanIuranScreen(
    feeRepo: FeeRepository? = null,
    refreshKey: Boolean = false,
    previewInvoices: List<FeeInvoiceDto>? = null,
    @DrawableRes backIcon: Int = R.drawable.panahkembali,
    onBack: (() -> Unit)? = null,
    onBayarClick: ((tahun: String, item: TagihanItem) -> Unit)? = null
) {
    val scope = rememberCoroutineScope()

    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var data by remember { mutableStateOf<List<TagihanTahun>>(emptyList()) }

    fun setFromInvoices(invoices: List<FeeInvoiceDto>) {
        data = buildGroupedTagihan(invoices)
        loading = false
        error = null
    }

    fun reload() {
        scope.launch {
            if (feeRepo == null) {
                val dummy = previewInvoices ?: dummyInvoices()
                setFromInvoices(dummy)
                return@launch
            }

            loading = true
            error = null
            try {
                val invoices = feeRepo.getInvoices()
                setFromInvoices(invoices)
            } catch (e: Exception) {
                error = e.message ?: "Gagal memuat tagihan"
                data = emptyList()
                loading = false
            }
        }
    }

    LaunchedEffect(Unit) { reload() }
    LaunchedEffect(refreshKey) { if (refreshKey) reload() }

    // Summary calculation (total unpaid)
    val totalUnpaid = remember(data) {
        data.flatMap { it.items }
            .filter { it.status.lowercase() != "paid" && it.status.lowercase() != "approved" && it.status.lowercase() != "pending" }
            .sumOf { it.rawNominal }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Brush.verticalGradient(listOf(BlueMain, BlueDark)))
    ) {
        // ===== HEADER Area =====
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
                    text = "Daftar Tagihan",
                    fontFamily = PoppinsSemi,
                    fontSize = 18.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(16.dp))

            // Illustration Hero
            Surface(
                modifier = Modifier.size(64.dp),
                shape = RoundedCornerShape(20.dp),
                color = Color.White.copy(alpha = 0.15f)
            ) {
                Box(contentAlignment = Alignment.Center) {
                    Icon(
                        imageVector = Icons.Default.Payments,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(32.dp)
                    )
                }
            }

            Spacer(Modifier.height(12.dp))
            Text(
                text = "Pantau dan selesaikan iuran wajib\ndengan praktis melalui aplikasi.",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                lineHeight = 16.sp
            )
        }

        // ===== CONTENT AREA =====
        Surface(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            color = Color.White
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 20.dp)
            ) {
                Spacer(Modifier.height(24.dp))

                // Summary Card (Premium Feel)
                if (!loading && error == null) {
                    Surface(
                        modifier = Modifier.fillMaxWidth().shadow(4.dp, RoundedCornerShape(24.dp)),
                        shape = RoundedCornerShape(24.dp),
                        color = BlueMain,
                    ) {
                        Row(
                            modifier = Modifier.padding(20.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Surface(
                                modifier = Modifier.size(48.dp),
                                shape = CircleShape,
                                color = Color.White.copy(alpha = 0.15f)
                            ) {
                                Box(contentAlignment = Alignment.Center) {
                                    Icon(Icons.Default.AccountBalanceWallet, null, tint = Color.White, modifier = Modifier.size(24.dp))
                                }
                            }
                            Spacer(Modifier.width(16.dp))
                            Column {
                                Text("Total Belum Dibayar", fontFamily = PoppinsReg, fontSize = 11.sp, color = Color.White.copy(alpha = 0.8f))
                                Text(formatRupiah(totalUnpaid), fontFamily = PoppinsSemi, fontSize = 20.sp, color = Color.White)
                            }
                        }
                    }
                    Spacer(Modifier.height(24.dp))
                }

                Text(
                    text = "Riwayat IPL Mendatang",
                    fontFamily = PoppinsSemi,
                    fontSize = 15.sp,
                    color = TextDark,
                    modifier = Modifier.padding(bottom = 12.dp)
                )

                when {
                    loading -> {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            CircularProgressIndicator(color = BlueMain, strokeWidth = 2.dp)
                        }
                    }

                    error != null -> {
                        Column(
                            modifier = Modifier.fillMaxSize(),
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center
                        ) {
                            Icon(Icons.Default.Info, null, tint = Color.Red.copy(alpha = 0.5f), modifier = Modifier.size(48.dp))
                            Spacer(Modifier.height(16.dp))
                            Text(error!!, fontFamily = PoppinsReg, color = TextMuted, textAlign = TextAlign.Center)
                            Spacer(Modifier.height(16.dp))
                            Button(onClick = { reload() }, colors = ButtonDefaults.buttonColors(containerColor = BlueMain)) {
                                Text("Coba Lagi", fontFamily = PoppinsSemi)
                            }
                        }
                    }

                    data.isEmpty() -> {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text("Tidak ada data tagihan.", fontFamily = PoppinsReg, color = TextMuted)
                        }
                    }

                    else -> {
                        LazyColumn(
                            verticalArrangement = Arrangement.spacedBy(20.dp),
                            contentPadding = PaddingValues(bottom = 32.dp)
                        ) {
                            items(data) { tahunSec ->
                                ModernTahunSection(
                                    tahunLabel = tahunSec.label,
                                    items = tahunSec.items,
                                    onBayarClick = { item -> onBayarClick?.invoke(tahunSec.label, item) }
                                )
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun ModernTahunSection(
    tahunLabel: String,
    items: List<TagihanItem>,
    onBayarClick: (TagihanItem) -> Unit
) {
    Column {
        Row(verticalAlignment = Alignment.CenterVertically) {
            Text(tahunLabel, fontFamily = PoppinsSemi, fontSize = 13.sp, color = BlueMain)
            Spacer(Modifier.width(8.dp))
            HorizontalDivider(modifier = Modifier.weight(1f), color = BorderColor, thickness = 1.dp)
        }
        Spacer(Modifier.height(12.dp))

        Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
            items.forEach { item ->
                InvoiceCard(item, onBayarClick)
            }
        }
    }
}

@Composable
private fun InvoiceCard(item: TagihanItem, onBayarClick: (TagihanItem) -> Unit) {
    val statusLower = item.status.lowercase()
    val isPaid = statusLower == "paid" || statusLower == "approved"
    val isPending = statusLower == "pending"
    val isUnpaid = !isPaid && !isPending

    Surface(
        modifier = Modifier.fillMaxWidth().clickable(enabled = isUnpaid) { onBayarClick(item) },
        shape = RoundedCornerShape(16.dp),
        color = Color.White,
        border = BorderStroke(1.dp, if (isUnpaid) BlueMain.copy(alpha = 0.2f) else BorderColor)
    ) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(item.bulan, fontFamily = PoppinsSemi, fontSize = 14.sp, color = TextDark)
                Spacer(Modifier.height(2.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text(item.nominal, fontFamily = PoppinsReg, fontSize = 13.sp, color = if (isUnpaid) AccentOrange else SuccessGreen)
                    if (isPending) {
                        Text(" • ", color = TextMuted)
                        Text("Diverifikasi", fontFamily = PoppinsReg, fontSize = 11.sp, color = PendingBlue)
                    }
                }
            }

            when {
                isPaid -> {
                    StatusChipSimple("Lunas", SuccessGreen)
                }
                isPending -> {
                    StatusChipSimple("Pending", PendingBlue)
                }
                else -> {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier
                            .clip(RoundedCornerShape(12.dp))
                            .background(AccentOrange.copy(alpha = 0.1f))
                            .padding(horizontal = 12.dp, vertical = 6.dp)
                    ) {
                        Text("Bayar", fontFamily = PoppinsSemi, fontSize = 11.sp, color = AccentOrange)
                        Spacer(Modifier.width(4.dp))
                        Icon(Icons.Default.ArrowForwardIos, null, tint = AccentOrange, modifier = Modifier.size(10.dp))
                    }
                }
            }
        }
    }
}

@Composable
private fun StatusChipSimple(label: String, color: Color) {
    Box(
        modifier = Modifier
            .background(color.copy(alpha = 0.12f), RoundedCornerShape(99.dp))
            .padding(horizontal = 12.dp, vertical = 4.dp)
    ) {
        Text(label, fontFamily = PoppinsSemi, fontSize = 10.sp, color = color)
    }
}

/* ===== Logic Helpers (RETAINED) ===== */
private fun buildGroupedTagihan(invoices: List<FeeInvoiceDto>): List<TagihanTahun> {
    val mapped = invoices.map { dto ->
        val periodYm = (dto.period ?: "0000-00").trim()
        val year = periodYm.take(4).toIntOrNull() ?: 0
        val bulanLabel = periodToIndoMonthYear(periodYm)
        val nominalStr = formatRupiah(dto.amount)

        val safeTrxId = dto.trxId?.takeIf { it.isNotBlank() } ?: "INV-${dto.id}"

        TagihanItem(
            invoiceId = dto.id,
            trxId = safeTrxId,
            bulan = bulanLabel,
            nominal = nominalStr,
            rawNominal = dto.amount,
            status = dto.status
        ) to year
    }

    return mapped
        .groupBy { it.second }
        .toSortedMap(compareByDescending { it })
        .map { (year, list) ->
            TagihanTahun(
                label = "IPL $year",
                items = list.map { it.first }.sortedByDescending { it.invoiceId }
            )
        }
}

private fun periodToIndoMonthYear(periodYm: String): String {
    val m = Regex("""(\d{4})-(\d{2})""").find(periodYm) ?: return "-"
    val year = m.groupValues[1]
    val month = m.groupValues[2].toIntOrNull() ?: return "-"
    val bulan = listOf(
        "Januari","Februari","Maret","April","Mei","Juni",
        "Juli","Agustus","September","Oktober","November","Desember"
    ).getOrNull(month - 1) ?: "-"
    return "$bulan $year"
}

private fun formatRupiah(amount: Long): String {
    // Basic Indonesian Currency Format
    val s = amount.toString()
    val sb = StringBuilder()
    var count = 0
    for (i in s.length - 1 downTo 0) {
        sb.append(s[i])
        count++
        if (count % 3 == 0 && i != 0) sb.append('.')
    }
    return "Rp. " + sb.reverse().toString()
}

private fun dummyInvoices(): List<FeeInvoiceDto> = listOf(
    FeeInvoiceDto(id = 5, amount = 150000, status = "rejected", period = "2025-08"),
    FeeInvoiceDto(id = 6, amount = 25000, status = "pending", period = "2025-07"),
    FeeInvoiceDto(id = 7, amount = 25000, status = "paid", period = "2025-06")
)

@Preview(showSystemUi = true)
@Composable
private fun PreviewTagihanModern() {
    MaterialTheme { 
        TagihanIuranScreen(previewInvoices = dummyInvoices()) 
    }
}
