@file:OptIn(ExperimentalMaterial3Api::class, ExperimentalFoundationApi::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.ExperimentalFoundationApi
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.relocation.BringIntoViewRequester
import androidx.compose.foundation.relocation.bringIntoViewRequester
import androidx.compose.foundation.rememberScrollState
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
fun FormSuratBelumMenikahScreen(
    onBack: () -> Unit = {},
    onKonfirmasi: () -> Unit = {}
) {
    val poppins = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    // ====== FORM STATE ======
    var nama by rememberSaveable { mutableStateOf("") }
    var nik by rememberSaveable { mutableStateOf("") }
    var tempatTglLahir by rememberSaveable { mutableStateOf("") }
    var jenisKelamin by rememberSaveable { mutableStateOf("") }
    var agama by rememberSaveable { mutableStateOf("") }
    var pekerjaan by rememberSaveable { mutableStateOf("") }
    var alamat by rememberSaveable { mutableStateOf("") }

    var keperluan by rememberSaveable { mutableStateOf("") }
    var tujuanInstansi by rememberSaveable { mutableStateOf("") }

    var showSuccess by rememberSaveable { mutableStateOf(false) }

    val canSubmit =
        nama.isNotBlank() &&
                nik.isNotBlank() &&
                tempatTglLahir.isNotBlank() &&
                jenisKelamin.isNotBlank() &&
                agama.isNotBlank() &&
                pekerjaan.isNotBlank() &&
                alamat.isNotBlank() &&
                keperluan.isNotBlank() &&
                tujuanInstansi.isNotBlank()

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
                    onClick = { showSuccess = true },
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

            // Back
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
                text = "Surat Keterangan Belum Menikah",
                fontFamily = poppinsSemi,
                fontWeight = FontWeight.SemiBold,
                color = Color.White,
                fontSize = 20.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.padding(horizontal = 22.dp)
            )

            Spacer(Modifier.height(8.dp))

            Text(
                text = "Isi data untuk pembuatan Surat Keterangan Belum Menikah.\nPastikan data sesuai identitas.",
                fontFamily = poppins,
                color = Color.White,
                fontSize = 13.sp,
                lineHeight = 18.sp,
                modifier = Modifier.padding(horizontal = 32.dp),
                textAlign = TextAlign.Center
            )

            Spacer(Modifier.height(22.dp))

            // Container putih
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
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(16.dp)
                            .imePadding()
                            .padding(bottom = 80.dp)
                    ) {
                        Text(
                            text = "Data Pemohon",
                            fontFamily = poppinsSemi,
                            fontWeight = FontWeight.SemiBold,
                            color = Color(0xFF1C6BA4),
                            fontSize = 14.sp
                        )

                        Spacer(Modifier.height(10.dp))

                        FieldBox(
                            label = "Nama",
                            value = nama,
                            onChange = { nama = it },
                            poppinsSemi = poppinsSemi,
                            placeholder = "Contoh: Siti Aisyah"
                        )

                        FieldBox(
                            label = "NIK",
                            value = nik,
                            onChange = { nik = it.filter { c -> c.isDigit() }.take(16) },
                            poppinsSemi = poppinsSemi,
                            placeholder = "16 digit NIK",
                            singleLine = true
                        )

                        FieldBox(
                            label = "Tempat/Tanggal Lahir",
                            value = tempatTglLahir,
                            onChange = { tempatTglLahir = it },
                            poppinsSemi = poppinsSemi,
                            placeholder = "Contoh: Batam, 10-10-2002"
                        )

                        FieldBox(
                            label = "Jenis Kelamin",
                            value = jenisKelamin,
                            onChange = { jenisKelamin = it },
                            poppinsSemi = poppinsSemi,
                            placeholder = "Contoh: Laki-laki / Perempuan"
                        )

                        FieldBox(
                            label = "Agama",
                            value = agama,
                            onChange = { agama = it },
                            poppinsSemi = poppinsSemi,
                            placeholder = "Contoh: Islam"
                        )

                        FieldBox(
                            label = "Pekerjaan",
                            value = pekerjaan,
                            onChange = { pekerjaan = it },
                            poppinsSemi = poppinsSemi,
                            placeholder = "Contoh: Karyawan Swasta"
                        )

                        FieldBox(
                            label = "Alamat",
                            value = alamat,
                            onChange = { alamat = it },
                            poppinsSemi = poppinsSemi,
                            placeholder = "Contoh: Blok A No. 7, Hawai Garden",
                            singleLine = false,
                            maxLines = 3,
                            height = 90.dp
                        )

                        DividerSoft()

                        Text(
                            text = "Keterangan Surat",
                            fontFamily = poppinsSemi,
                            fontWeight = FontWeight.SemiBold,
                            color = Color(0xFF1C6BA4),
                            fontSize = 14.sp,
                            modifier = Modifier.padding(top = 6.dp)
                        )

                        FieldBox(
                            label = "Keperluan",
                            value = keperluan,
                            onChange = { keperluan = it },
                            poppinsSemi = poppinsSemi,
                            placeholder = "Contoh: Persyaratan administrasi pernikahan",
                            singleLine = false,
                            maxLines = 3,
                            height = 90.dp
                        )

                        FieldBox(
                            label = "Tujuan/Instansi",
                            value = tujuanInstansi,
                            onChange = { tujuanInstansi = it },
                            poppinsSemi = poppinsSemi,
                            placeholder = "Contoh: KUA / Kelurahan / Instansi"
                        )

                        Spacer(Modifier.height(8.dp))
                    }
                }
            }
        }
    }

    if (showSuccess) {
        SuccessPopup(
            title = "Pengajuan Berhasil!",
            subtitle = "Surat Belum Menikah sudah dikirim.\nSilakan pantau status pengajuan.",
            onFinished = {
                showSuccess = false
                onKonfirmasi()
            }
        )
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

@Composable
private fun SuccessPopup(
    title: String,
    subtitle: String,
    onFinished: () -> Unit
) {
    val scope = rememberCoroutineScope()

    LaunchedEffect(Unit) {
        scope.launch {
            delay(900)
            onFinished()
        }
    }

    AlertDialog(
        onDismissRequest = { /* auto */ },
        confirmButton = {},
        title = {
            Text(
                text = title,
                fontWeight = FontWeight.Bold,
                fontSize = 18.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth()
            )
        },
        text = {
            Text(
                text = subtitle,
                fontSize = 13.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth()
            )
        }
    )
}

/* =================== PREVIEW =================== */
@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewFormSuratBelumMenikah() {
    MaterialTheme {
        FormSuratBelumMenikahScreen(
            onBack = {},
            onKonfirmasi = {}
        )
    }
}
