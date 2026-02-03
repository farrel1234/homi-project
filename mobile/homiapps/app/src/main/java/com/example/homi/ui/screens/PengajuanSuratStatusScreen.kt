// File: app/src/main/java/com/example/homi/ui/screens/PengajuanSuratStatusScreen.kt
package com.example.homi.ui.screens

import android.content.ContentValues
import android.content.Context
import android.media.MediaScannerConnection
import android.os.Build
import android.os.Environment
import android.provider.MediaStore
import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.ServiceRequestDto
import com.example.homi.data.repository.ServiceRequestRepository
import kotlinx.coroutines.launch
import java.io.File
import java.io.FileOutputStream

// ✅ Accompanist SwipeRefresh
import com.google.accompanist.swiperefresh.SwipeRefresh
import com.google.accompanist.swiperefresh.rememberSwipeRefreshState

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PengajuanSuratStatusScreen(
    id: Long,
    repo: ServiceRequestRepository,
    onBack: () -> Unit
) {
    // ===== COLORS =====
    val BlueMain = Color(0xFF2F79A0)
    val BlueBorder = Color(0xFF2F79A0)
    val OrangeBtn = Color(0xFFF6A47A)
    val TextMuted = Color(0xFF6B7280)
    val CardBg = Color.White

    // ===== Fonts =====
    val poppinsReg = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    val ctx = LocalContext.current
    val scope = rememberCoroutineScope()

    var loading by remember { mutableStateOf(true) }
    var refreshing by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var item by remember { mutableStateOf<ServiceRequestDto?>(null) }
    var downloading by remember { mutableStateOf(false) }

    fun normalizeStatus(raw: String?): String {
        val s = raw?.trim()?.lowercase() ?: ""
        return when {
            s == "approved" || s == "disetujui" -> "approved"
            s == "rejected" || s == "ditolak" -> "rejected"
            s == "pending" || s == "process" || s == "diproses" || s == "in_progress" -> "processing"
            else -> s.ifBlank { "processing" }
        }
    }

    fun statusLabel(norm: String): String = when (norm) {
        "approved" -> "Disetujui"
        "rejected" -> "Ditolak"
        else -> "Diproses"
    }

    fun statusChipBg(norm: String): Color = when (norm) {
        "approved" -> Color(0xFFE7F6EC)
        "rejected" -> Color(0xFFFDECEC)
        else -> Color(0xFFFFF3D6)
    }

    fun statusChipText(norm: String): Color = when (norm) {
        "approved" -> Color(0xFF16A34A)
        "rejected" -> Color(0xFFDC2626)
        else -> Color(0xFFF97316)
    }

    fun load(refresh: Boolean) {
        scope.launch {
            if (refresh) refreshing = true else loading = true
            error = null
            try {
                item = repo.detail(id) // ✅ fetch terbaru dari server
            } catch (e: Exception) {
                error = e.message ?: "Gagal load detail"
            } finally {
                if (refresh) refreshing = false else loading = false
            }
        }
    }

    LaunchedEffect(id) { load(refresh = false) }

    val swipeState = rememberSwipeRefreshState(isRefreshing = refreshing)

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        // ===== HEADER =====
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 14.dp, vertical = 12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            IconButton(onClick = onBack) {
                Icon(
                    imageVector = Icons.Default.ArrowBack,
                    contentDescription = "Kembali",
                    tint = Color.White
                )
            }

            Text(
                text = "Status Pengajuan",
                fontFamily = poppinsSemi,
                fontWeight = FontWeight.SemiBold,
                fontSize = 20.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )

            // ✅ tombol refresh kecil
            IconButton(
                onClick = { load(refresh = true) },
                enabled = !refreshing && !loading
            ) {
                Icon(
                    imageVector = Icons.Default.Refresh,
                    contentDescription = "Refresh",
                    tint = Color.White
                )
            }
        }

        Text(
            text = "Pantau proses pengajuan surat kamu.\nTarik layar ke bawah untuk memperbarui status.",
            fontFamily = poppinsReg,
            fontSize = 12.sp,
            color = Color.White,
            textAlign = TextAlign.Center,
            lineHeight = 16.sp,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 28.dp)
        )

        Spacer(Modifier.height(16.dp))

        // ===== CONTAINER PUTIH + SWIPE REFRESH =====
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Color.White, shape = RoundedCornerShape(topStart = 36.dp, topEnd = 36.dp))
        ) {
            SwipeRefresh(
                state = swipeState,
                onRefresh = { load(refresh = true) },
                modifier = Modifier.fillMaxSize()
            ) {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 18.dp, vertical = 18.dp)
                ) {
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
                                    color = MaterialTheme.colorScheme.error,
                                    fontFamily = poppinsReg,
                                    textAlign = TextAlign.Center
                                )
                                Spacer(Modifier.height(12.dp))
                                Button(onClick = { load(refresh = false) }) {
                                    Text("Coba lagi", fontFamily = poppinsSemi)
                                }
                            }
                        }

                        else -> {
                            val data = item
                            if (data == null) {
                                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                                    Text("Data tidak ditemukan.", fontFamily = poppinsReg, color = TextMuted)
                                }
                            } else {
                                val norm = normalizeStatus(data.status)
                                val canDownload = norm == "approved" && !data.pdfUrl.isNullOrBlank()

                                val nomorPengajuan = "REQ-${id.toString().padStart(4, '0')}"
                                val tanggalLabel = data.requestDate ?: "-"

                                Column(
                                    modifier = Modifier
                                        .fillMaxSize()
                                        .verticalScroll(rememberScrollState())
                                ) {
                                    Card(
                                        colors = CardDefaults.cardColors(containerColor = CardBg),
                                        shape = RoundedCornerShape(18.dp),
                                        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp),
                                        border = BorderStroke(2.dp, BlueBorder),
                                        modifier = Modifier.fillMaxWidth()
                                    ) {
                                        Column(
                                            modifier = Modifier
                                                .fillMaxWidth()
                                                .padding(16.dp)
                                        ) {
                                            Text(
                                                text = "Ringkasan Pengajuan",
                                                fontFamily = poppinsSemi,
                                                fontWeight = FontWeight.SemiBold,
                                                fontSize = 14.sp,
                                                color = BlueBorder
                                            )

                                            Spacer(Modifier.height(14.dp))

                                            InfoRow(
                                                label = "Jenis Surat",
                                                value = data.type?.name ?: "-",
                                                fontReg = poppinsReg,
                                                fontSemi = poppinsSemi
                                            )
                                            Spacer(Modifier.height(8.dp))

                                            InfoRow(
                                                label = "Nomor Pengajuan",
                                                value = nomorPengajuan,
                                                fontReg = poppinsReg,
                                                fontSemi = poppinsSemi
                                            )
                                            Spacer(Modifier.height(8.dp))

                                            InfoRow(
                                                label = "Tanggal",
                                                value = tanggalLabel,
                                                fontReg = poppinsReg,
                                                fontSemi = poppinsSemi
                                            )

                                            Spacer(Modifier.height(14.dp))

                                            Box(
                                                modifier = Modifier
                                                    .background(statusChipBg(norm), RoundedCornerShape(10.dp))
                                                    .padding(horizontal = 12.dp, vertical = 8.dp)
                                            ) {
                                                Text(
                                                    text = "Status : ${statusLabel(norm)}",
                                                    fontFamily = poppinsSemi,
                                                    fontSize = 12.sp,
                                                    color = statusChipText(norm)
                                                )
                                            }

                                            Spacer(Modifier.height(14.dp))

                                            Button(
                                                onClick = {
                                                    scope.launch {
                                                        downloading = true
                                                        try {
                                                            val bytes = repo.downloadPdfBytes(id)
                                                            val ok = savePdfToDownloads(ctx, "surat_$id.pdf", bytes)
                                                            if (ok) {
                                                                Toast.makeText(ctx, "PDF tersimpan di Download/HOMI", Toast.LENGTH_LONG).show()
                                                            } else {
                                                                Toast.makeText(ctx, "Gagal simpan PDF", Toast.LENGTH_LONG).show()
                                                            }
                                                        } catch (e: Exception) {
                                                            Toast.makeText(ctx, e.message ?: "Download gagal", Toast.LENGTH_LONG).show()
                                                        } finally {
                                                            downloading = false
                                                        }
                                                    }
                                                },
                                                enabled = canDownload && !downloading,
                                                shape = RoundedCornerShape(12.dp),
                                                colors = ButtonDefaults.buttonColors(
                                                    containerColor = OrangeBtn,
                                                    disabledContainerColor = OrangeBtn.copy(alpha = 0.45f)
                                                ),
                                                modifier = Modifier
                                                    .fillMaxWidth()
                                                    .height(46.dp)
                                            ) {
                                                if (downloading) {
                                                    CircularProgressIndicator(
                                                        modifier = Modifier.size(18.dp),
                                                        strokeWidth = 2.dp,
                                                        color = Color.White
                                                    )
                                                    Spacer(Modifier.width(10.dp))
                                                }
                                                Text(
                                                    text = "Download PDF",
                                                    fontFamily = poppinsSemi,
                                                    color = Color.White
                                                )
                                            }

                                            Spacer(Modifier.height(10.dp))

                                            Text(
                                                text = if (canDownload) "PDF siap diunduh"
                                                else "PDF akan aktif setelah admin menyetujui pengajuan.",
                                                fontFamily = poppinsReg,
                                                fontSize = 11.sp,
                                                color = TextMuted
                                            )

                                            if (!data.adminNote.isNullOrBlank()) {
                                                Spacer(Modifier.height(10.dp))
                                                Text(
                                                    text = "Catatan admin: ${data.adminNote}",
                                                    fontFamily = poppinsReg,
                                                    fontSize = 11.sp,
                                                    color = TextMuted
                                                )
                                            }
                                        }
                                    }

                                    Spacer(Modifier.height(18.dp))
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

/**
 * Row tampilan "Label : Value"
 */
@Composable
private fun InfoRow(
    label: String,
    value: String,
    fontReg: FontFamily,
    fontSemi: FontFamily
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.Top
    ) {
        Text(
            text = label,
            fontFamily = fontReg,
            fontSize = 12.sp,
            color = Color(0xFF111827),
            modifier = Modifier.weight(1f)
        )
        Text(
            text = " : ",
            fontFamily = fontReg,
            fontSize = 12.sp,
            color = Color(0xFF111827)
        )
        Text(
            text = value,
            fontFamily = fontSemi,
            fontSize = 12.sp,
            color = Color(0xFF111827),
            modifier = Modifier.weight(1f)
        )
    }
}

fun savePdfToDownloads(context: Context, fileName: String, bytes: ByteArray): Boolean {
    return try {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            val resolver = context.contentResolver
            val values = ContentValues().apply {
                put(MediaStore.MediaColumns.DISPLAY_NAME, fileName)
                put(MediaStore.MediaColumns.MIME_TYPE, "application/pdf")
                put(MediaStore.MediaColumns.RELATIVE_PATH, Environment.DIRECTORY_DOWNLOADS + "/HOMI")
                put(MediaStore.MediaColumns.IS_PENDING, 1)
            }

            val uri = resolver.insert(MediaStore.Downloads.EXTERNAL_CONTENT_URI, values) ?: return false
            resolver.openOutputStream(uri)?.use { it.write(bytes) } ?: return false

            values.clear()
            values.put(MediaStore.MediaColumns.IS_PENDING, 0)
            resolver.update(uri, values, null, null)

            true
        } else {
            val downloadsDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)
            val homiDir = File(downloadsDir, "HOMI")
            if (!homiDir.exists()) homiDir.mkdirs()

            val outFile = File(homiDir, fileName)
            FileOutputStream(outFile).use { it.write(bytes) }

            MediaScannerConnection.scanFile(
                context,
                arrayOf(outFile.absolutePath),
                arrayOf("application/pdf"),
                null
            )
            true
        }
    } catch (e: Exception) {
        e.printStackTrace()
        false
    }
}
