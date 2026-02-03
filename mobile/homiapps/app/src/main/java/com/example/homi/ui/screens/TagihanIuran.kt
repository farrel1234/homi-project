// File: TagihanIuranScreen.kt
package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.FeeInvoiceDto
import com.example.homi.data.repository.FeeRepository
import kotlinx.coroutines.launch

/* ===== THEME COLORS ===== */
private val BlueMain = Color(0xFF2F7FA3)
private val BlueBorder = Color(0xFF2F7FA3)
private val BlueText = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val RowBg = Color(0xFFF7F7F7)
private val TextDark = Color(0xFF0E0E0E)
private val PaidGreen = Color(0xFF2EAD67)
private val LineGray = Color(0xFFE6E6E6)
private val PendingBlue = Color(0xFF2563EB)

/* ===== FONTS ===== */
private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

/* ===== DATA CLASSES ===== */
data class TagihanItem(
    val invoiceId: Long,
    val trxId: String, // NON-NULL
    val bulan: String,
    val nominal: String,
    val status: String
)

data class TagihanTahun(val label: String, val items: List<TagihanItem>)

/* ===== SCREEN ===== */
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
            IconButton(onClick = { onBack?.invoke() }, modifier = Modifier.size(48.dp)) {
                Icon(
                    painter = painterResource(id = backIcon),
                    contentDescription = "Kembali",
                    tint = Color.White,
                    modifier = Modifier.size(28.dp)
                )
            }

            Text(
                text = "Tagihan",
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
            fontSize = 14.sp,
            color = Color.White,
            textAlign = TextAlign.Center,
            lineHeight = 18.sp,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp)
        )

        Spacer(Modifier.height(12.dp))

        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            border = BorderStroke(2.dp, BlueBorder),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(16.dp)
            ) {
                Text(
                    text = "Iuran Wajib",
                    fontFamily = PoppinsSemi,
                    fontSize = 16.sp,
                    color = BlueText,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center
                )

                Spacer(Modifier.height(12.dp))

                when {
                    loading -> {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            CircularProgressIndicator()
                        }
                    }

                    error != null -> {
                        Column(
                            modifier = Modifier.fillMaxSize(),
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center
                        ) {
                            Text(
                                text = error!!,
                                fontFamily = PoppinsReg,
                                color = Color(0xFFDC2626),
                                textAlign = TextAlign.Center
                            )
                            Spacer(Modifier.height(10.dp))
                            OutlinedButton(onClick = { reload() }) {
                                Text("Coba Lagi", fontFamily = PoppinsSemi, color = BlueText)
                            }
                        }
                    }

                    data.isEmpty() -> {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text("Belum ada tagihan.", fontFamily = PoppinsReg, color = Color(0xFF64748B))
                        }
                    }

                    else -> {
                        LazyColumn(
                            verticalArrangement = Arrangement.spacedBy(14.dp),
                            modifier = Modifier.fillMaxSize()
                        ) {
                            items(data.size) { idx ->
                                TahunSection(
                                    tahun = data[idx].label,
                                    items = data[idx].items,
                                    onBayarClick = { item -> onBayarClick?.invoke(data[idx].label, item) }
                                )
                            }
                        }
                    }
                }
            }
        }
    }
}

/* ===== TAHUN SECTION ===== */
@Composable
private fun TahunSection(
    tahun: String,
    items: List<TagihanItem>,
    onBayarClick: (TagihanItem) -> Unit
) {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .border(1.dp, LineGray, RoundedCornerShape(10.dp))
            .padding(12.dp)
    ) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .clip(RoundedCornerShape(10.dp))
                .background(AccentOrange)
                .padding(vertical = 10.dp, horizontal = 14.dp)
        ) {
            Text(text = tahun, fontFamily = PoppinsSemi, fontSize = 14.sp, color = Color.White)
        }

        Spacer(Modifier.height(10.dp))

        items.forEachIndexed { i, item ->
            val st = item.status.lowercase()

            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(if (i % 2 == 0) RowBg else Color.White)
                    .padding(horizontal = 12.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = item.bulan,
                    fontFamily = PoppinsReg,
                    fontSize = 13.sp,
                    color = TextDark,
                    modifier = Modifier.weight(1.2f)
                )
                Text(
                    text = item.nominal,
                    fontFamily = PoppinsReg,
                    fontSize = 13.sp,
                    color = TextDark,
                    modifier = Modifier.weight(1f)
                )

                when {
                    st == "paid" || st == "approved" -> {
                        Text(
                            text = "Sudah Dibayar",
                            fontFamily = PoppinsSemi,
                            fontSize = 10.sp,
                            color = PaidGreen,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.weight(0.9f)
                        )
                    }

                    st == "pending" -> {
                        Text(
                            text = "Menunggu\nVerifikasi",
                            fontFamily = PoppinsSemi,
                            fontSize = 10.sp,
                            color = PendingBlue,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.weight(0.9f)
                        )
                    }

                    else -> {
                        OutlinedButton(
                            onClick = { onBayarClick(item) },
                            border = BorderStroke(1.dp, AccentOrange),
                            colors = ButtonDefaults.outlinedButtonColors(contentColor = AccentOrange),
                            shape = RoundedCornerShape(6.dp),
                            contentPadding = PaddingValues(horizontal = 20.dp, vertical = 6.dp),
                            modifier = Modifier
                                .height(28.dp)
                                .widthIn(min = 91.dp)
                        ) {
                            Text("Bayar", fontFamily = PoppinsSemi, fontSize = 12.sp)
                        }
                    }
                }
            }

            if (i != items.lastIndex) Divider(color = LineGray, thickness = 1.dp)
        }
    }
}

/* ===== Helpers (FIX ANTI NULL) ===== */
private fun buildGroupedTagihan(invoices: List<FeeInvoiceDto>): List<TagihanTahun> {
    val mapped = invoices.map { dto ->
        val periodYm = (dto.period ?: "0000-00").trim() // aman
        val year = periodYm.take(4).toIntOrNull() ?: 0
        val bulanLabel = periodToIndoMonthYear(periodYm)
        val nominal = formatRupiah(dto.amount)

        val safeTrxId = dto.trxId?.takeIf { it.isNotBlank() } ?: "INV-${dto.id}" // ✅ fallback biar non-null

        TagihanItem(
            invoiceId = dto.id,
            trxId = safeTrxId,
            bulan = bulanLabel,
            nominal = nominal,
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


private fun safePeriodYm(periodRaw: String?): String {
    val raw = (periodRaw ?: "").trim()
    val m = Regex("""(\d{4})-(\d{2})""").find(raw)
    return m?.value ?: "0000-00"
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
    FeeInvoiceDto(
        id = 5,
        amount = 150000,
        status = "rejected",
        trxId = "IPL-5VDHMRD2VK",
        period = "2025-08",
        dueDate = "2025-08-31",
        feeType = "Iuran Sampah"
    ),
    FeeInvoiceDto(
        id = 6,
        amount = 25000,
        status = "pending",
        trxId = "IPL-XXXX000006",
        period = "2025-07",
        dueDate = "2025-07-31",
        feeType = "Iuran Sampah"
    ),
    FeeInvoiceDto(
        id = 7,
        amount = 25000,
        status = "paid",
        trxId = "IPL-XXXX000007",
        period = "2025-06",
        dueDate = "2025-06-30",
        feeType = "Iuran Sampah"
    )
)

@Preview(showSystemUi = true, showBackground = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewTagihan() {
    MaterialTheme {
        TagihanIuranScreen(
            feeRepo = null,
            onBack = {},
            onBayarClick = { _, _ -> }
        )
    }
}
