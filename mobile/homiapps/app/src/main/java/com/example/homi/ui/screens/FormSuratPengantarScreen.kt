@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

@Composable
fun FormSuratPengantarScreen(
    onBack: () -> Unit = {},
    onKonfirmasi: (Map<String, String>) -> Unit = {}
) {
    val poppins = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    // === fields sesuai gambar ===
    var nama by remember { mutableStateOf("") }
    var nik by remember { mutableStateOf("") }
    var alamat by remember { mutableStateOf("") }
    var blokNo by remember { mutableStateOf("") }
    var keperluan by remember { mutableStateOf("") }

    var showError by remember { mutableStateOf(false) }

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
            text = "Formulir Pengajuan",
            fontFamily = poppinsSemi,
            fontWeight = FontWeight.SemiBold,
            color = Color.White,
            fontSize = 22.sp
        )

        Spacer(Modifier.height(8.dp))

        Text(
            text = "Silahkan mengisi data formulir dibawah ini,\nuntuk melakukan pengajuan layanan:",
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
                    // Field box seperti gambar
                    FieldBox("Nama Lengkap", nama, { nama = it }, poppins, poppinsSemi)
                    FieldBox("NIK", nik, { nik = it }, poppins, poppinsSemi)
                    FieldBox("Alamat Domisili", alamat, { alamat = it }, poppins, poppinsSemi)
                    FieldBox("Blok/No Rumah", blokNo, { blokNo = it }, poppins, poppinsSemi)
                    FieldBox("Keperluan", keperluan, { keperluan = it }, poppins, poppinsSemi)

                    if (showError) {
                        Text(
                            text = "Lengkapi semua data yang wajib diisi.",
                            fontFamily = poppins,
                            color = Color(0xFFEF4444),
                            fontSize = 12.sp,
                            modifier = Modifier.padding(top = 8.dp)
                        )
                    }

                    Spacer(Modifier.height(26.dp))

                    // ✅ Tombol Konfirmasi (ADA & posisinya bawah)
                    Button(
                        onClick = {
                            val valid = nama.isNotBlank() &&
                                    nik.isNotBlank() &&
                                    alamat.isNotBlank() &&
                                    blokNo.isNotBlank() &&
                                    keperluan.isNotBlank()

                            if (!valid) {
                                showError = true
                                return@Button
                            }
                            showError = false

                            onKonfirmasi(
                                mapOf(
                                    "nama" to nama.trim(),
                                    "nik" to nik.trim(),
                                    "alamat" to alamat.trim(),
                                    "blok_no" to blokNo.trim(),
                                    "keperluan" to keperluan.trim()
                                )
                            )
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFFA06B)),
                        shape = RoundedCornerShape(12.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(46.dp)
                            .padding(horizontal = 10.dp)
                    ) {
                        Text(
                            text = "Konfirmasi",
                            fontFamily = poppinsSemi,
                            fontWeight = FontWeight.SemiBold,
                            color = Color.White,
                            fontSize = 14.sp
                        )
                    }

                    Spacer(Modifier.height(8.dp))
                }
            }
        }
    }
}

@Composable
private fun FieldBox(
    label: String,
    value: String,
    onChange: (String) -> Unit,
    poppins: FontFamily,
    poppinsSemi: FontFamily
) {
    Text(
        text = label,
        fontFamily = poppinsSemi,
        fontWeight = FontWeight.SemiBold,
        color = Color(0xFF1C6BA4),
        fontSize = 13.sp,
        modifier = Modifier.padding(top = 10.dp, bottom = 6.dp)
    )

    OutlinedTextField(
        value = value,
        onValueChange = onChange,
        modifier = Modifier
            .fillMaxWidth()
            .height(46.dp),
        singleLine = true,
        textStyle = TextStyle(
            fontFamily = poppins,
            fontSize = 13.sp,
            color = Color.Black
        ),
        shape = RoundedCornerShape(6.dp),
        colors = OutlinedTextFieldDefaults.colors(
            focusedBorderColor = Color(0xFFD1D5DB),
            unfocusedBorderColor = Color(0xFFD1D5DB),
            focusedContainerColor = Color(0xFFF3F4F6),
            unfocusedContainerColor = Color(0xFFF3F4F6),
            cursorColor = Color(0xFF1C6BA4)
        )
    )
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
fun PreviewFormSuratPengantar_Simple() {
    MaterialTheme { FormSuratPengantarScreen() }
}
