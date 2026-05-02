package com.example.homi.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.HomeWork
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TenantSelectionScreen(
    onCodeConfirmed: (String) -> Unit
) {
    var housingCode by remember { mutableStateOf("") }
    var showError by remember { mutableStateOf(false) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(24.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Icon(
            imageVector = Icons.Default.HomeWork,
            contentDescription = null,
            modifier = Modifier.size(100.dp),
            tint = Color(0xFF1E3A8A)
        )

        Spacer(modifier = Modifier.height(32.dp))

        Text(
            text = "Selamat Datang di HOMI",
            style = MaterialTheme.typography.headlineMedium,
            fontWeight = FontWeight.Bold,
            color = Color(0xFF1E3A8A)
        )

        Text(
            text = "Silakan masukkan kode perumahan Anda",
            style = MaterialTheme.typography.bodyMedium,
            color = Color.Gray,
            modifier = Modifier.padding(top = 8.dp)
        )

        Spacer(modifier = Modifier.height(32.dp))

        OutlinedTextField(
            value = housingCode,
            onValueChange = { 
                housingCode = it.uppercase()
                showError = false
            },
            label = { Text("Kode Perumahan") },
            placeholder = { Text("Contoh: CENTRAL") },
            modifier = Modifier.fillMaxWidth(),
            singleLine = true,
            isError = showError,
            shape = RoundedCornerShape(12.dp),
            colors = OutlinedTextFieldDefaults.colors(
                focusedBorderColor = Color(0xFF1E3A8A),
                focusedLabelColor = Color(0xFF1E3A8A)
            )
        )
        
        if (showError) {
            Text(
                text = "Kode tidak boleh kosong",
                color = MaterialTheme.colorScheme.error,
                style = MaterialTheme.typography.bodySmall,
                modifier = Modifier.align(Alignment.Start).padding(start = 8.dp, top = 4.dp)
            )
        }

        Spacer(modifier = Modifier.height(24.dp))

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
                .height(56.dp),
            shape = RoundedCornerShape(12.dp),
            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1E3A8A))
        ) {
            Text("Masuk", color = Color.White, fontSize = 18.sp, fontWeight = FontWeight.Bold)
        }
        
        Spacer(modifier = Modifier.height(16.dp))
        
        Text(
            text = "Hubungi pengelola perumahan jika Anda tidak mengetahui kodenya.",
            style = MaterialTheme.typography.bodySmall,
            color = Color.LightGray,
            textAlign = androidx.compose.ui.text.style.TextAlign.Center
        )
    }
}
