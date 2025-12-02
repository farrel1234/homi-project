@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.ChevronRight
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
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

@Composable
fun FormAjuan1(
    onBack: () -> Unit = {},
    onKonfirmasi: () -> Unit = {}
) {
    val poppins = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }

    var nama by remember { mutableStateOf("") }
    var jenisAjuan by remember { mutableStateOf<String?>(null) }
    var tanggal by remember { mutableStateOf("") }
    var tempat by remember { mutableStateOf("") }
    var perihal by remember { mutableStateOf("") }

    val opsiAjuan = listOf("Surat Keterangan", "Izin Keramaian", "Perbaikan Fasilitas")

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
            fontFamily = poppins,
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
            textAlign = androidx.compose.ui.text.style.TextAlign.Center
        )

        Spacer(Modifier.height(30.dp))

        // Container putih
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Color.White, shape = RoundedCornerShape(topStart = 40.dp, topEnd = 40.dp))
                .padding(horizontal = 24.dp, vertical = 32.dp)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .border(
                        BorderStroke(1.dp, Color(0xFF1C6BA4)),
                        RoundedCornerShape(16.dp)
                    )
                    .padding(16.dp)
            ) {
                TextItem(label = "Nama Pelapor", poppins = poppins)
                LineTextField(value = nama, onValueChange = { nama = it }, poppins = poppins)

                TextItem(label = "Jenis Pengajuan", poppins = poppins)
                UnderlineDropdown(
                    options = opsiAjuan,
                    selected = jenisAjuan,
                    onSelected = { jenisAjuan = it },
                    placeholder = "Pilih Jenis Ajuan",
                    font = poppins
                )

                TextItem(label = "Tanggal", poppins = poppins)
                LineTextField(value = tanggal, onValueChange = { tanggal = it }, poppins = poppins)

                TextItem(label = "Tempat", poppins = poppins)
                LineTextField(value = tempat, onValueChange = { tempat = it }, poppins = poppins)

                TextItem(label = "Perihal", poppins = poppins)
                LineTextField(value = perihal, onValueChange = { perihal = it }, poppins = poppins)

                Spacer(Modifier.height(30.dp))

                Button(
                    onClick = onKonfirmasi,
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFFA06B)),
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp)
                ) {
                    Text(
                        text = "Konfirmasi",
                        color = Color.White,
                        fontFamily = poppins,
                        fontWeight = FontWeight.SemiBold,
                        fontSize = 15.sp
                    )
                }
            }
        }
    }
}

/* ---------- Subcomponents ---------- */

@Composable
fun TextItem(label: String, poppins: FontFamily) {
    Text(
        text = label,
        fontFamily = poppins,
        fontWeight = FontWeight.SemiBold,
        color = Color(0xFF1C6BA4),
        fontSize = 14.sp,
        modifier = Modifier.padding(top = 12.dp, bottom = 4.dp)
    )
}

@Composable
fun LineTextField(
    value: String,
    onValueChange: (String) -> Unit,
    poppins: FontFamily,
    trailing: Boolean = false
) {
    TextField(
        value = value,
        onValueChange = onValueChange,
        textStyle = TextStyle(
            fontFamily = poppins,
            fontSize = 14.sp,
            color = Color.Black
        ),
        modifier = Modifier.fillMaxWidth(),
        singleLine = true,
        trailingIcon = {
            if (trailing) {
                Text(
                    text = ">",
                    fontFamily = poppins,
                    color = Color(0xFF1C6BA4),
                    fontWeight = FontWeight.Bold,
                    fontSize = 23.sp
                )
            }
        },
        colors = TextFieldDefaults.colors(
            focusedContainerColor = Color.Transparent,
            unfocusedContainerColor = Color.Transparent,
            focusedIndicatorColor = Color(0xFFBBBBBB),
            unfocusedIndicatorColor = Color(0xFFBBBBBB),
            cursorColor = Color.Black
        )
    )
}

/* ---------- Dropdown gaya underline ---------- */

@Composable
fun UnderlineDropdown(
    options: List<String>,
    selected: String?,
    onSelected: (String) -> Unit,
    placeholder: String = "Pilih Jenis Ajuan",
    font: FontFamily = FontFamily.Default
) {
    var expanded by remember { mutableStateOf(false) }
    val isPlaceholder = selected.isNullOrBlank()

    Column(modifier = Modifier.fillMaxWidth()) {

        Row(
            modifier = Modifier
                .fillMaxWidth()
                .clickable { expanded = true }
                .padding(vertical = 14.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                text = if (isPlaceholder) placeholder else selected!!,
                fontFamily = font,
                fontSize = 14.sp,
                color = if (isPlaceholder) Color(0xFF8AA0B2) else Color(0xFF0E0E0E),
                modifier = Modifier.weight(1f)
            )

            Icon(
                imageVector = Icons.Outlined.ChevronRight,
                contentDescription = "Buka pilihan",
                tint = Color(0xFF1C6BA4),
                modifier = Modifier.size(18.dp)
            )
        }

        Divider(
            color = Color(0xFFBBBBBB),
            thickness = 1.dp
        )

        DropdownMenu(
            expanded = expanded,
            onDismissRequest = { expanded = false },
            modifier = Modifier.fillMaxWidth()
        ) {
            options.forEach { option ->
                DropdownMenuItem(
                    text = {
                        Text(
                            text = option,
                            fontFamily = font,
                            fontSize = 14.sp,
                            color = Color(0xFF3C4A56)
                        )
                    },
                    onClick = {
                        onSelected(option)
                        expanded = false
                    }
                )
            }
        }
    }
}

/* ---------- Preview ---------- */

@Preview(showBackground = true, showSystemUi = true)
@Composable
fun PreviewFormAjuan1() {
    FormAjuan1()
}
