@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
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

enum class SuratStatus(val label: String) {
    SUBMITTED("Diajukan"),
    PROCESSED("Diproses"),
    APPROVED("Disetujui"),
    REJECTED("Ditolak")
}

@Composable
fun PengajuanSuratStatusScreen(
    onBack: () -> Unit = {},
    jenisSurat: String = "Surat Domisili",
    nomorPengajuan: String = "REQ-0001",
    tanggal: String = "19 Desember 2025",
    status: SuratStatus = SuratStatus.PROCESSED,
    onDownloadPdf: () -> Unit = {}
) {
    val poppins = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    val statusColor = when (status) {
        SuratStatus.SUBMITTED -> Color(0xFF64748B)
        SuratStatus.PROCESSED -> Color(0xFFF59E0B)
        SuratStatus.APPROVED  -> Color(0xFF16A34A)
        SuratStatus.REJECTED  -> Color(0xFFEF4444)
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF2F79A0)),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Spacer(modifier = Modifier.height(40.dp))

        // Back
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .padding(start = 20.dp)
        ) {
            Image(
                painter = painterResource(id = R.drawable.panahkembali),
                contentDescription = "Kembali",
                modifier = Modifier
                    .size(26.dp)
                    .align(Alignment.CenterStart)
                    .clickable { onBack() }
            )
        }

        Spacer(Modifier.height(8.dp))

        Text(
            text = "Status Pengajuan",
            fontFamily = poppinsSemi,
            fontWeight = FontWeight.SemiBold,
            color = Color.White,
            fontSize = 22.sp
        )

        Spacer(Modifier.height(8.dp))

        Text(
            text = "Pantau proses pengajuan surat kamu.\nFile PDF bisa diunduh setelah disetujui admin.",
            fontFamily = poppins,
            color = Color.White,
            fontSize = 13.sp,
            lineHeight = 18.sp,
            modifier = Modifier.padding(horizontal = 32.dp),
            textAlign = TextAlign.Center
        )

        Spacer(Modifier.height(26.dp))

        // Container putih
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Color.White,
                    shape = RoundedCornerShape(topStart = 40.dp, topEnd = 40.dp)
                )
                .padding(horizontal = 22.dp, vertical = 22.dp)
        ) {
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .border(
                        BorderStroke(1.dp, Color(0xFF1C6BA4)),
                        RoundedCornerShape(18.dp)
                    ),
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
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
                        color = Color(0xFF1C6BA4),
                        fontSize = 14.sp
                    )

                    Spacer(Modifier.height(12.dp))

                    InfoRow("Jenis Surat", jenisSurat, poppins, poppinsSemi)
                    InfoRow("Nomor Pengajuan", nomorPengajuan, poppins, poppinsSemi)
                    InfoRow("Tanggal", tanggal, poppins, poppinsSemi)

                    Spacer(Modifier.height(10.dp))

                    // Badge status
                    Box(
                        modifier = Modifier
                            .background(statusColor.copy(alpha = 0.12f), RoundedCornerShape(10.dp))
                            .padding(horizontal = 12.dp, vertical = 8.dp)
                    ) {
                        Text(
                            text = "Status: ${status.label}",
                            fontFamily = poppinsSemi,
                            color = statusColor,
                            fontSize = 13.sp
                        )
                    }

                    Spacer(Modifier.height(18.dp))

                    val canDownload = (status == SuratStatus.APPROVED)

                    Button(
                        onClick = { if (canDownload) onDownloadPdf() },
                        enabled = canDownload,
                        colors = ButtonDefaults.buttonColors(
                            containerColor = Color(0xFFFFA06B),
                            disabledContainerColor = Color(0xFFFFA06B).copy(alpha = 0.45f)
                        ),
                        shape = RoundedCornerShape(12.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(46.dp)
                            .padding(horizontal = 10.dp)
                    ) {
                        Text(
                            text = "Download PDF",
                            fontFamily = poppinsSemi,
                            fontWeight = FontWeight.SemiBold,
                            color = Color.White,
                            fontSize = 14.sp
                        )
                    }

                    Spacer(Modifier.height(8.dp))

                    Text(
                        text = if (canDownload)
                            "PDF siap diunduh."
                        else
                            "PDF akan aktif setelah admin menyetujui pengajuan.",
                        fontFamily = poppins,
                        color = Color(0xFF64748B),
                        fontSize = 12.sp
                    )
                }
            }
        }
    }
}

@Composable
private fun InfoRow(
    label: String,
    value: String,
    poppins: FontFamily,
    poppinsSemi: FontFamily
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.Top
    ) {
        Text(
            text = label,
            fontFamily = poppinsSemi,
            color = Color(0xFF1C6BA4),
            fontSize = 12.sp,
            modifier = Modifier.weight(0.45f)
        )
        Text(
            text = ":",
            fontFamily = poppins,
            color = Color(0xFF94A3B8),
            fontSize = 12.sp,
            modifier = Modifier.padding(horizontal = 6.dp)
        )
        Text(
            text = value,
            fontFamily = poppins,
            color = Color(0xFF111827),
            fontSize = 12.sp,
            modifier = Modifier.weight(0.55f)
        )
    }
    Spacer(Modifier.height(8.dp))
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
fun PreviewStatusPengajuan() {
    MaterialTheme {
        PengajuanSuratStatusScreen(status = SuratStatus.PROCESSED)
    }
}
