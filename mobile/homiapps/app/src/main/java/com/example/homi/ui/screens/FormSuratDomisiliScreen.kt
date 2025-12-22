// File: FormSuratDomisiliScreen.kt
@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.ExperimentalFoundationApi
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.relocation.BringIntoViewRequester
import androidx.compose.foundation.relocation.bringIntoViewRequester
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.focus.onFocusEvent
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

private val BlueMain = Color(0xFF2F79A0)
private val BlueBorder = Color(0xFF1C6BA4)
private val AccentOrange = Color(0xFFFFA06B)

@Composable
fun FormSuratDomisiliScreen(
    onBack: () -> Unit = {},
    onKonfirmasi: (Map<String, String>) -> Unit = {}
) {
    val poppins = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    var nama by rememberSaveable { mutableStateOf("") }
    var nik by rememberSaveable { mutableStateOf("") }
    var alamat by rememberSaveable { mutableStateOf("") }
    var blok by rememberSaveable { mutableStateOf("") }
    var noRumah by rememberSaveable { mutableStateOf("") }
    var keperluan by rememberSaveable { mutableStateOf("") }

    val canSubmit = nama.isNotBlank() && nik.isNotBlank() && alamat.isNotBlank()

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        // ===== HEADER (tetap di atas) =====
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 10.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(
                painter = painterResource(id = R.drawable.panahkembali),
                contentDescription = "Kembali",
                tint = Color.White,
                modifier = Modifier
                    .size(26.dp)
                    .clickable { onBack() }
            )

            Spacer(Modifier.width(10.dp))

            Text(
                text = "Surat Domisili",
                fontFamily = poppinsSemi,
                fontSize = 20.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )

            Spacer(Modifier.width(26.dp))
        }

        Text(
            text = "Isi data di bawah ini. Form akan otomatis naik\nsaat keyboard muncul.",
            fontFamily = poppins,
            fontSize = 12.sp,
            color = Color.White.copy(alpha = 0.9f),
            textAlign = TextAlign.Center,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp, vertical = 6.dp)
        )

        Spacer(Modifier.height(10.dp))

        // ===== CONTAINER PUTIH =====
        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 28.dp, topEnd = 28.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .imePadding()
                    .navigationBarsPadding()
                    .padding(horizontal = 16.dp, vertical = 16.dp)
            ) {
                Text(
                    text = "Data Pemohon",
                    fontFamily = poppinsSemi,
                    fontSize = 14.sp,
                    color = BlueBorder
                )

                Spacer(Modifier.height(12.dp))

                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(16.dp),
                    border = BorderStroke(1.dp, BlueBorder),
                    colors = CardDefaults.cardColors(containerColor = Color.White)
                ) {
                    Column(modifier = Modifier.padding(14.dp)) {

                        FormField(
                            label = "Nama Lengkap",
                            placeholder = "Isi Nama Lengkap",
                            value = nama,
                            onChange = { nama = it },
                            poppins = poppins,
                            poppinsSemi = poppinsSemi,
                            keyboardType = KeyboardType.Text
                        )

                        Spacer(Modifier.height(10.dp))

                        FormField(
                            label = "NIK",
                            placeholder = "Isi NIK",
                            value = nik,
                            onChange = { nik = it },
                            poppins = poppins,
                            poppinsSemi = poppinsSemi,
                            keyboardType = KeyboardType.Number
                        )

                        Spacer(Modifier.height(10.dp))

                        FormField(
                            label = "Alamat",
                            placeholder = "Isi Alamat",
                            value = alamat,
                            onChange = { alamat = it },
                            poppins = poppins,
                            poppinsSemi = poppinsSemi,
                            keyboardType = KeyboardType.Text,
                            singleLine = false,
                            minLines = 2
                        )

                        Spacer(Modifier.height(10.dp))

                        // ✅ FIX: Blok & No Rumah dibuat 1 kolom (atas-bawah)
                        FormField(
                            label = "Blok",
                            placeholder = "Isi Blok",
                            value = blok,
                            onChange = { blok = it },
                            poppins = poppins,
                            poppinsSemi = poppinsSemi,
                            keyboardType = KeyboardType.Text
                        )

                        Spacer(Modifier.height(10.dp))

                        FormField(
                            label = "No. Rumah",
                            placeholder = "Isi No. Rumah",
                            value = noRumah,
                            onChange = { noRumah = it },
                            poppins = poppins,
                            poppinsSemi = poppinsSemi,
                            keyboardType = KeyboardType.Text
                        )

                        Spacer(Modifier.height(10.dp))

                        FormField(
                            label = "Keperluan",
                            placeholder = "Isi Keperluan",
                            value = keperluan,
                            onChange = { keperluan = it },
                            poppins = poppins,
                            poppinsSemi = poppinsSemi,
                            keyboardType = KeyboardType.Text,
                            singleLine = false,
                            minLines = 2
                        )

                        Spacer(Modifier.height(16.dp))

                        Button(
                            onClick = {
                                val payload = mapOf(
                                    "nama" to nama,
                                    "nik" to nik,
                                    "alamat" to alamat,
                                    "blok" to blok,
                                    "no_rumah" to noRumah,
                                    "keperluan" to keperluan
                                )
                                onKonfirmasi(payload)
                            },
                            enabled = canSubmit,
                            colors = ButtonDefaults.buttonColors(
                                containerColor = AccentOrange,
                                disabledContainerColor = AccentOrange.copy(alpha = 0.45f)
                            ),
                            shape = RoundedCornerShape(12.dp),
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(46.dp)
                        ) {
                            Text(
                                text = "Konfirmasi",
                                fontFamily = poppinsSemi,
                                color = Color.White,
                                fontSize = 14.sp
                            )
                        }

                        Spacer(Modifier.height(8.dp))

                        Text(
                            text = if (canSubmit) "Siap dikonfirmasi." else "Lengkapi minimal: Nama, NIK, Alamat.",
                            fontFamily = poppins,
                            fontSize = 12.sp,
                            color = if (canSubmit) Color(0xFF16A34A) else Color(0xFFEF4444)
                        )
                    }
                }

                Spacer(Modifier.height(22.dp))
            }
        }
    }
}

@OptIn(ExperimentalFoundationApi::class)
@Composable
private fun FormField(
    label: String,
    placeholder: String,
    value: String,
    onChange: (String) -> Unit,
    poppins: FontFamily,
    poppinsSemi: FontFamily,
    keyboardType: KeyboardType,
    singleLine: Boolean = true,
    minLines: Int = 1
) {
    val bringIntoViewRequester = remember { BringIntoViewRequester() }
    val scope = rememberCoroutineScope()

    Text(
        text = label,
        fontFamily = poppinsSemi,
        fontWeight = FontWeight.SemiBold,
        color = BlueBorder,
        fontSize = 13.sp,
        modifier = Modifier.padding(bottom = 6.dp)
    )

    OutlinedTextField(
        value = value,
        onValueChange = onChange,
        placeholder = {
            Text(
                text = placeholder,
                fontFamily = poppins,
                fontSize = 12.sp,
                color = Color(0xFF94A3B8)
            )
        },
        singleLine = singleLine,
        minLines = minLines,
        keyboardOptions = KeyboardOptions(keyboardType = keyboardType),
        modifier = Modifier
            .fillMaxWidth()
            .bringIntoViewRequester(bringIntoViewRequester)
            .onFocusEvent {
                if (it.isFocused) {
                    scope.launch {
                        delay(150)
                        bringIntoViewRequester.bringIntoView()
                    }
                }
            },
        shape = RoundedCornerShape(12.dp),
        colors = OutlinedTextFieldDefaults.colors(
            focusedBorderColor = BlueBorder,
            unfocusedBorderColor = Color(0xFFE2E8F0),
            cursorColor = BlueBorder
        )
    )
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewFormSuratDomisili() {
    MaterialTheme {
        FormSuratDomisiliScreen()
    }
}
