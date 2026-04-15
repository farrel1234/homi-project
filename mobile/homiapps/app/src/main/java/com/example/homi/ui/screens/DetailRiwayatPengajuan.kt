@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
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
import com.example.homi.data.model.ServiceRequestDto
import com.example.homi.data.repository.ServiceRequestRepository
import kotlinx.coroutines.launch

/* ===== Tokens ===== */
private val BlueHeader  = Color(0xFF2F7FA3)
private val TextDark    = Color(0xFF0E0E0E)
private val TextMuted   = Color(0xFF6B7280)

private val PoppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }
private val PoppinsReg  = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }

@Composable
fun DetailRiwayatPengajuan(
    serviceRequestId: Long,
    serviceRepo: ServiceRequestRepository? = null,
    onBack: () -> Unit
) {
    var loading by remember { mutableStateOf(serviceRepo != null) }
    var error by remember { mutableStateOf<String?>(null) }
    var data by remember { mutableStateOf<ServiceRequestDto?>(null) }
    val scope = rememberCoroutineScope()

    LaunchedEffect(serviceRequestId) {
        if (serviceRepo == null) return@LaunchedEffect
        loading = true; error = null
        try { data = serviceRepo.detail(serviceRequestId) }
        catch (e: Exception) { error = e.message ?: "Gagal memuat detail" }
        finally { loading = false }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Brush.verticalGradient(listOf(BlueHeader, Color(0xFF1A5E7B))))
    ) {
        // ===== HEADER =====
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(220.dp)
                .statusBarsPadding()
        ) {
            IconButton(
                onClick = onBack,
                modifier = Modifier.padding(start = 10.dp, top = 6.dp)
            ) {
                Icon(
                    painter = painterResource(id = R.drawable.panahkembali),
                    contentDescription = "Kembali",
                    tint = Color.White
                )
            }

            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 22.dp, vertical = 16.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Text(
                    text = "Detail Pengajuan",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    textAlign = TextAlign.Center
                )
                Spacer(Modifier.height(8.dp))
                Text(
                    text = "Lacak status permohonan surat atau\nlayanan lingkungan Anda secara real-time.",
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = Color.White.copy(alpha = 0.85f),
                    textAlign = TextAlign.Center,
                    lineHeight = 18.sp
                )
            }
        }

        // ===== BODY =====
        Surface(
            color = Color.White,
            shape = RoundedCornerShape(topStart = 36.dp, topEnd = 36.dp),
            modifier = Modifier
                .fillMaxSize()
                .offset(y = (-28).dp)
        ) {
            if (loading) {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = BlueHeader)
                }
            } else if (error != null) {
                Column(
                    Modifier.fillMaxSize().padding(24.dp),
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.Center
                ) {
                    Icon(Icons.Outlined.ErrorOutline, null, tint = Color.Red, modifier = Modifier.size(48.dp))
                    Spacer(Modifier.height(16.dp))
                    Text(error!!, textAlign = TextAlign.Center)
                }
            } else if (data != null) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState())
                        .padding(horizontal = 16.dp, vertical = 18.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    DetailCard(
                        ticketId = data?.id.toString(),
                        status = data?.status ?: "baru",
                        typeName = data?.type?.name ?: "Layanan Umum"
                    )
                    
                    Spacer(Modifier.height(16.dp))
                    
                    DetailItemCard(
                        title = "Data Pengajuan",
                        content = {
                            DetailRow(Icons.Outlined.Assignment, "Jenis Layanan", data?.type?.name ?: "-")
                            DetailRow(Icons.Outlined.Person, "Nama Pelapor", data?.reporterName ?: "-")
                            DetailRow(Icons.Outlined.CalendarMonth, "Tanggal Pengajuan", data?.requestDate ?: "-")
                        }
                    )

                    Spacer(Modifier.height(12.dp))

                    DetailItemCard(
                        title = "Informasi Tambahan",
                        content = {
                            DetailRow(Icons.Outlined.Description, "Perihal", data?.subject ?: "-")
                            DetailRow(Icons.Outlined.LocationOn, "Lokasi", data?.place ?: "-")
                        }
                    )

                    if (!data?.adminNote.isNullOrBlank()) {
                        Spacer(Modifier.height(12.dp))
                        DetailItemCard(
                            title = "Catatan Admin",
                            content = {
                                Text(
                                    text = data?.adminNote!!,
                                    fontFamily = PoppinsReg,
                                    fontSize = 14.sp,
                                    color = Color(0xFFB91C1C),
                                    lineHeight = 22.sp
                                )
                            }
                        )
                    }

                    Spacer(Modifier.height(80.dp))
                }
            }
        }
    }
}

@Composable
private fun DetailCard(ticketId: String, status: String, typeName: String) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0xFFF8FAFC)),
        border = BorderStroke(1.dp, Color(0xFFE2E8F0))
    ) {
        Column(Modifier.padding(16.dp)) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                StatusChip(status)
                Spacer(Modifier.weight(1f))
                Text(typeName, fontFamily = PoppinsSemi, fontSize = 11.sp, color = BlueHeader)
            }
            Spacer(Modifier.height(12.dp))
            Text(text = "ID Pengajuan: #$ticketId", fontFamily = PoppinsReg, fontSize = 13.sp, color = TextMuted)
        }
    }
}

@Composable
private fun StatusChip(status: String) {
    val (bg, fg) = when (status.lowercase()) {
        "baru", "pending", "submitted" -> Color(0xFFEFF6FF) to Color(0xFF1D4ED8)
        "selesai", "approved", "done", "paid"  -> Color(0xFFECFDF3) to Color(0xFF047857)
        "ditolak", "rejected"         -> Color(0xFFFEF2F2) to Color(0xFFB91C1C)
        else                          -> Color(0xFFF3F4F6) to Color(0xFF374151)
    }

    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(999.dp))
            .background(bg)
            .padding(horizontal = 10.dp, vertical = 6.dp)
    ) {
        Text(
            text = status.uppercase(),
            fontFamily = PoppinsSemi,
            fontSize = 10.sp,
            color = fg
        )
    }
}

@Composable
private fun DetailItemCard(title: String, content: @Composable () -> Unit) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        border = BorderStroke(1.dp, Color(0xFFF1F5F9))
    ) {
        Column(Modifier.padding(16.dp)) {
            Text(title, fontFamily = PoppinsSemi, fontSize = 13.sp, color = BlueHeader)
            Spacer(Modifier.height(12.dp))
            content()
        }
    }
}

@Composable
private fun DetailRow(icon: androidx.compose.ui.graphics.vector.ImageVector, label: String, value: String) {
    Row(Modifier.fillMaxWidth().padding(vertical = 4.dp), verticalAlignment = Alignment.CenterVertically) {
        Icon(icon, null, tint = BlueHeader.copy(alpha = 0.6f), modifier = Modifier.size(18.dp))
        Spacer(Modifier.width(12.dp))
        Column {
            Text(label, fontFamily = PoppinsReg, fontSize = 11.sp, color = TextMuted)
            Text(value, fontFamily = PoppinsSemi, fontSize = 14.sp, color = TextDark)
        }
    }
}
