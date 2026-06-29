package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TenantSelectionScreen(
    onCodeConfirmed: (String) -> Unit
) {
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val blue = Color(0xFF256D85)
    val orange = Color(0xFFFFA06B)

    var housingCode by remember { mutableStateOf("") }
    var showError by remember { mutableStateOf(false) }

    Box(modifier = Modifier.fillMaxSize()) {
        // Premium Background Image
        Image(
            painter = painterResource(id = R.drawable.login),
            contentDescription = "Background",
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop
        )

        Column(
            modifier = Modifier
                .fillMaxSize()
                .imePadding()
        ) {
            // Flexible space to push the card down
            Spacer(modifier = Modifier.weight(1f))

            // Sheet Container
            Surface(
                modifier = Modifier
                    .fillMaxWidth()
                    .clip(RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp)),
                color = Color.White.copy(alpha = 0.95f),
                tonalElevation = 8.dp
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .verticalScroll(rememberScrollState())
                        .padding(horizontal = 24.dp, vertical = 32.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Text(
                        text = "Selamat Datang di HOMI",
                        fontSize = 22.sp,
                        fontWeight = FontWeight.Bold,
                        fontFamily = poppins,
                        color = blue,
                        modifier = Modifier.padding(bottom = 8.dp),
                        textAlign = TextAlign.Center
                    )

                    Text(
                        text = "Untuk masuk, silakan input Kode Perumahan Anda yang terdaftar pada sistem pusat.",
                        fontSize = 13.sp,
                        color = Color.Gray,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.padding(bottom = 24.dp)
                    )

                    // Text Field
                    OutlinedTextField(
                        value = housingCode,
                        onValueChange = {
                            housingCode = it.uppercase()
                            showError = false
                        },
                        label = { Text("Kode Perumahan", fontFamily = poppins) },
                        placeholder = { Text("Contoh: HAWAII-GARDEN") },
                        modifier = Modifier.fillMaxWidth(),
                        singleLine = true,
                        isError = showError,
                        shape = RoundedCornerShape(12.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = blue,
                            unfocusedBorderColor = Color.Gray,
                            focusedContainerColor = Color(0xFFF8F8F8),
                            unfocusedContainerColor = Color(0xFFF8F8F8)
                        )
                    )

                    if (showError) {
                        Text(
                            text = "Kode perumahan wajib diisi.",
                            color = Color.Red,
                            style = MaterialTheme.typography.bodySmall,
                            modifier = Modifier
                                .align(Alignment.Start)
                                .padding(start = 8.dp, top = 4.dp)
                        )
                    }

                    Spacer(modifier = Modifier.height(24.dp))

                    // Button
                    Button(
                        onClick = {
                            if (housingCode.isNotBlank()) {
                                onCodeConfirmed(housingCode.trim())
                            } else {
                                showError = true
                            }
                        },
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(54.dp),
                        shape = RoundedCornerShape(12.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = orange)
                    ) {
                        Text(
                            text = "Lanjutkan",
                            color = Color.White,
                            fontFamily = poppins,
                            fontWeight = FontWeight.Bold,
                            fontSize = 16.sp
                        )
                    }

                    Spacer(modifier = Modifier.height(20.dp))

                    Text(
                        text = "Catatan: Hubungi pengelola perumahan Anda jika belum memiliki kode perumahan.",
                        style = MaterialTheme.typography.bodySmall,
                        color = Color.Gray,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.padding(horizontal = 12.dp)
                    )

                    Spacer(modifier = Modifier.height(16.dp))
                }
            }
        }
    }
}
