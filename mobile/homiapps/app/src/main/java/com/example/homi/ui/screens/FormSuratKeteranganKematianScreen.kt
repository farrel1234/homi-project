@file:OptIn(ExperimentalMaterial3Api::class, ExperimentalFoundationApi::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.ExperimentalFoundationApi
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.relocation.BringIntoViewRequester
import androidx.compose.foundation.relocation.bringIntoViewRequester
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.focus.onFocusEvent
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

@Composable
fun FormSuratKeteranganKematianScreen(
    onBack: () -> Unit = {},
    onKonfirmasi: (Map<String, String>) -> Unit = {}
) {
    val poppins = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    // Pelapor
    var namaPelapor by rememberSaveable { mutableStateOf("") }
    var nikPelapor by rememberSaveable { mutableStateOf("") }
    var alamatPelapor by rememberSaveable { mutableStateOf("") }
    var hubungan by rememberSaveable { mutableStateOf("") }

    // Almarhum/Almarhumah
    var namaAlm by rememberSaveable { mutableStateOf("") }
    var nikAlm by rememberSaveable { mutableStateOf("") }
    var ttlAlm by rememberSaveable { mutableStateOf("") }
    var alamatAlm by rememberSaveable { mutableStateOf("") }

    // Detail kematian
    var tanggalKematian by rememberSaveable { mutableStateOf("") }
    var tempatKematian by rememberSaveable { mutableStateOf("") }
    var penyebab by rememberSaveable { mutableStateOf("") }
    var keperluan by rememberSaveable { mutableStateOf("") }

    val canSubmit =
        namaPelapor.isNotBlank() &&
                nikPelapor.isNotBlank() &&
                alamatPelapor.isNotBlank() &&
                hubungan.isNotBlank() &&
                namaAlm.isNotBlank() &&
                nikAlm.isNotBlank() &&
                ttlAlm.isNotBlank() &&
                alamatAlm.isNotBlank() &&
                tanggalKematian.isNotBlank() &&
                tempatKematian.isNotBlank() &&
                penyebab.isNotBlank() &&
                keperluan.isNotBlank()

    Scaffold(
        containerColor = Color(0xFF2F79A0),
        bottomBar = {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(Color.White)
                    .imePadding()
                    .navigationBarsPadding()
                    .padding(horizontal = 22.dp, vertical = 14.dp)
            ) {
                Button(
                    onClick = {
                        val payload = mapOf(
                            "nama_pelapor" to namaPelapor,
                            "nik_pelapor" to nikPelapor,
                            "alamat_pelapor" to alamatPelapor,
                            "hubungan" to hubungan,

                            "nama_alm" to namaAlm,
                            "nik_alm" to nikAlm,
                            "ttl_alm" to ttlAlm,
                            "alamat_alm" to alamatAlm,

                            "tanggal_kematian" to tanggalKematian,
                            "tempat_kematian" to tempatKematian,
                            "penyebab" to penyebab,
                            "keperluan" to keperluan
                        )
                        onKonfirmasi(payload)
                    },
                    enabled = canSubmit,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = Color(0xFFFFA06B),
                        disabledContainerColor = Color(0xFFFFA06B).copy(alpha = 0.45f)
                    ),
                    shape = RoundedCornerShape(12.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp)
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

                Text(
                    text = if (canSubmit) "Data sudah lengkap, kamu bisa submit."
                    else "Lengkapi semua data dulu ya.",
                    fontFamily = poppins,
                    color = if (canSubmit) Color(0xFF16A34A) else Color(0xFFEF4444),
                    fontSize = 12.sp
                )
            }
        }
    ) { innerPadding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
                .background(Color(0xFF2F79A0)),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Spacer(modifier = Modifier.height(40.dp))

            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(start = 20.dp)
            ) {
                Icon(
                    painter = painterResource(id = R.drawable.panahkembali),
                    contentDescription = "Kembali",
                    tint = Color.Unspecified,
                    modifier = Modifier
                        .size(26.dp)
                        .align(Alignment.CenterStart)
                        .clickable { onBack() }
                )
            }

            Spacer(Modifier.height(8.dp))

            Text(
                text = "Surat Keterangan Kematian",
                fontFamily = poppinsSemi,
                fontWeight = FontWeight.SemiBold,
                color = Color.White,
                fontSize = 22.sp
            )

            Spacer(Modifier.height(8.dp))

            Text(
                text = "Isi data untuk pembuatan Surat Keterangan Kematian.\nPastikan data almarhum dan pelapor sesuai.",
                fontFamily = poppins,
                color = Color.White,
                fontSize = 13.sp,
                lineHeight = 18.sp,
                modifier = Modifier.padding(horizontal = 32.dp),
                textAlign = TextAlign.Center
            )

            Spacer(Modifier.height(22.dp))

            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(
                        Color.White,
                        shape = RoundedCornerShape(topStart = 40.dp, topEnd = 40.dp)
                    )
                    .padding(horizontal = 22.dp, vertical = 18.dp)
            ) {
                Card(
                    modifier = Modifier
                        .fillMaxSize()
                        .border(
                            width = 1.dp,
                            color = Color(0xFF1C6BA4),
                            shape = RoundedCornerShape(18.dp)
                        ),
                    shape = RoundedCornerShape(18.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    val scrollState = rememberScrollState()

                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(scrollState)
                            .padding(16.dp)
                            .imePadding()
                            .padding(bottom = 80.dp)
                    ) {
                        Text(
                            text = "Data Pelapor",
                            fontFamily = poppinsSemi,
                            fontWeight = FontWeight.SemiBold,
                            color = Color(0xFF1C6BA4),
                            fontSize = 14.sp
                        )

                        Spacer(Modifier.height(10.dp))

                        FieldBox("Nama Pelapor", namaPelapor, { namaPelapor = it }, poppinsSemi, "Contoh: Budi Santoso")
                        FieldBox("NIK Pelapor", nikPelapor, { nikPelapor = it.filter { c -> c.isDigit() }.take(16) }, poppinsSemi, "16 digit NIK", true)
                        FieldBox("Alamat Pelapor", alamatPelapor, { alamatPelapor = it }, poppinsSemi, "Contoh: Blok A No. 7, Hawai Garden", false, 3, 90.dp)
                        FieldBox("Hubungan dengan Almarhum/Almarhumah", hubungan, { hubungan = it }, poppinsSemi, "Contoh: Anak / Istri / Suami / Keluarga")

                        DividerSoft()

                        Text(
                            text = "Data Almarhum/Almarhumah",
                            fontFamily = poppinsSemi,
                            fontWeight = FontWeight.SemiBold,
                            color = Color(0xFF1C6BA4),
                            fontSize = 14.sp,
                            modifier = Modifier.padding(top = 6.dp)
                        )

                        FieldBox("Nama Almarhum/Almarhumah", namaAlm, { namaAlm = it }, poppinsSemi, "Nama sesuai KTP/KK")
                        FieldBox("NIK Almarhum/Almarhumah", nikAlm, { nikAlm = it.filter { c -> c.isDigit() }.take(16) }, poppinsSemi, "16 digit NIK", true)
                        FieldBox("Tempat/Tanggal Lahir", ttlAlm, { ttlAlm = it }, poppinsSemi, "Contoh: Batam, 10-10-1980")
                        FieldBox("Alamat Almarhum/Almarhumah", alamatAlm, { alamatAlm = it }, poppinsSemi, "Alamat sesuai KK/KTP", false, 3, 90.dp)

                        DividerSoft()

                        Text(
                            text = "Detail Kematian",
                            fontFamily = poppinsSemi,
                            fontWeight = FontWeight.SemiBold,
                            color = Color(0xFF1C6BA4),
                            fontSize = 14.sp,
                            modifier = Modifier.padding(top = 6.dp)
                        )

                        FieldBox("Tanggal Kematian", tanggalKematian, { tanggalKematian = it }, poppinsSemi, "Contoh: 19-12-2025")
                        FieldBox("Tempat Kematian", tempatKematian, { tempatKematian = it }, poppinsSemi, "Contoh: RS / Rumah / Lokasi")
                        FieldBox("Penyebab Kematian", penyebab, { penyebab = it }, poppinsSemi, "Contoh: Sakit / Kecelakaan / Lainnya")
                        FieldBox("Keperluan", keperluan, { keperluan = it }, poppinsSemi, "Contoh: Administrasi kependudukan", false, 3, 90.dp)

                        Spacer(Modifier.height(8.dp))
                    }
                }
            }
        }
    }
}

@Composable
private fun DividerSoft() {
    Spacer(Modifier.height(14.dp))
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(1.dp)
            .background(Color(0xFFE5E7EB))
    )
    Spacer(Modifier.height(10.dp))
}

@OptIn(ExperimentalFoundationApi::class)
@Composable
private fun FieldBox(
    label: String,
    value: String,
    onChange: (String) -> Unit,
    poppinsSemi: FontFamily,
    placeholder: String = "",
    singleLine: Boolean = true,
    maxLines: Int = 1,
    height: Dp = 46.dp
) {
    val bringIntoViewRequester = remember { BringIntoViewRequester() }
    val scope = rememberCoroutineScope()

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
            .height(height)
            .bringIntoViewRequester(bringIntoViewRequester)
            .onFocusEvent { fs ->
                if (fs.isFocused) {
                    scope.launch {
                        delay(200)
                        bringIntoViewRequester.bringIntoView()
                    }
                }
            },
        textStyle = TextStyle(fontFamily = poppinsSemi, fontSize = 14.sp),
        placeholder = {
            if (placeholder.isNotBlank()) {
                Text(
                    placeholder,
                    fontFamily = poppinsSemi,
                    fontSize = 13.sp,
                    color = Color(0xFF94A3B8)
                )
            }
        },
        singleLine = singleLine,
        maxLines = maxLines,
        shape = RoundedCornerShape(10.dp),
        colors = OutlinedTextFieldDefaults.colors(
            focusedBorderColor = Color(0xFF1C6BA4),
            unfocusedBorderColor = Color(0xFFD1D5DB),
            focusedContainerColor = Color(0xFFF3F4F6),
            unfocusedContainerColor = Color(0xFFF3F4F6),
            cursorColor = Color(0xFF1C6BA4)
        )
    )
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewFormSuratKeteranganKematian() {
    MaterialTheme { FormSuratKeteranganKematianScreen() }
}
