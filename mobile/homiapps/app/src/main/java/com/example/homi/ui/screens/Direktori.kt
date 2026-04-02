package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Info
import androidx.compose.material.icons.filled.ChevronRight
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.local.TokenStore
import com.example.homi.data.model.DirectoryItem
import com.example.homi.data.remote.ApiClient
import kotlinx.coroutines.delay

/* ===== Tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueBorder   = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val TextPrimary  = Color(0xFF0E0E0E)
private val TextMuted    = Color(0xFF8A8A8A)
private val LineGray     = Color(0xFFE6E6E6)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

/* ===== UI Model (untuk tabel kamu) ===== */
private data class DirektoriItemUi(
    val nama: String,
    val alamat: String
)

private fun DirectoryItem.toUi(): DirektoriItemUi {
    val alamat = when {
        !blokAlamat.isNullOrBlank() -> blokAlamat!!
        !blok.isNullOrBlank() && !noRumah.isNullOrBlank() -> "Blok $blok No $noRumah"
        !blok.isNullOrBlank() -> "Blok $blok"
        else -> "-"
    }
    return DirektoriItemUi(
        nama = name,
        alamat = alamat
    )
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DirektoriScreen(
    tokenStore: TokenStore,
    onBack: (() -> Unit)? = null,
    @DrawableRes backIcon: Int = R.drawable.panahkembali
) {
    val context = LocalContext.current
    val api = remember { ApiClient.getApi(tokenStore) }

    var query by remember { mutableStateOf("") }

    // state API
    var loading by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var rows by remember { mutableStateOf<List<DirektoriItemUi>>(emptyList()) }

    // load awal + debounce search (server-side)
    LaunchedEffect(Unit) {
        loading = true
        error = null
        try {
            val res = api.getDirectory(null)
            rows = res.data.map { it.toUi() }
        } catch (e: Exception) {
            error = e.message ?: "Gagal memuat direktori"
        } finally {
            loading = false
        }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        Spacer(Modifier.height(8.dp))

        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(28.dp)
                    .clickable(enabled = onBack != null) { onBack?.invoke() },
                contentAlignment = Alignment.Center
            ) {
                if (onBack != null) {
                    Image(
                        painter = painterResource(id = backIcon),
                        contentDescription = "Kembali",
                        modifier = Modifier.size(24.dp)
                    )
                } else {
                    Spacer(Modifier.size(24.dp))
                }
            }

            Text(
                text = "Direktori",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )

            Spacer(Modifier.size(28.dp))
        }

        Spacer(Modifier.height(6.dp))
        Text(
            text = "Berikut adalah nama dan alamat warga perumahan\nHawaii Garden",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = Color.White,
            lineHeight = 18.sp,
            textAlign = TextAlign.Center,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp)
        )

        Spacer(Modifier.height(18.dp))
        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(16.dp)
            ) {

                OutlinedTextField(
                    value = query,
                    onValueChange = { query = it },
                    singleLine = true,
                    shape = RoundedCornerShape(24.dp),
                    placeholder = {
                        Text(
                            "Cari nama / blok / rumah...",
                            fontFamily = PoppinsReg,
                            fontSize = 13.sp,
                            color = TextMuted
                        )
                    },
                    leadingIcon = { Text("🔍", fontSize = 16.sp) },
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = BlueBorder,
                        unfocusedBorderColor = BlueBorder,
                        cursorColor = BlueBorder
                    ),
                    modifier = Modifier.fillMaxWidth(),
                    textStyle = androidx.compose.ui.text.TextStyle(
                        fontFamily = PoppinsReg,
                        fontSize = 13.sp
                    ),
                )

                Spacer(Modifier.height(14.dp))


                when {
                    loading -> {
                        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            CircularProgressIndicator(color = BlueMain)
                        }
                    }
                    error != null -> {
                        Box(modifier = Modifier.fillMaxSize().padding(24.dp), contentAlignment = Alignment.Center) {
                            Text(text = "Error: $error", color = TextMuted, textAlign = TextAlign.Center)
                        }
                    }
                    rows.isEmpty() -> {
                        Box(modifier = Modifier.fillMaxSize().padding(24.dp), contentAlignment = Alignment.Center) {
                            Text(text = if (query.isBlank()) "Direktori kosong" else "Tidak ada hasil", color = TextMuted)
                        }
                    }
                    else -> {
                        LazyColumn(
                            modifier = Modifier.fillMaxSize(),
                            contentPadding = PaddingValues(bottom = 80.dp),
                            verticalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            items(rows) { item ->
                                DirectoryCard(item)
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun DirectoryCard(item: DirektoriItemUi) {
    val initial = item.nama.take(1).uppercase()
    // Identicon color logic (simple hash)
    val bgColors = listOf(
        Color(0xFFE3F2FD), Color(0xFFF3E5F5), Color(0xFFE8F5E9), 
        Color(0xFFFFF3E0), Color(0xFFFBE9E7), Color(0xFFE0F2F1)
    )
    val textColors = listOf(
        Color(0xFF1E88E5), Color(0xFF8E24AA), Color(0xFF43A047), 
        Color(0xFFFB8C00), Color(0xFFF4511E), Color(0xFF00897B)
    )
    val index = (item.nama.hashCode() % bgColors.size).let { if (it < 0) -it else it }
    
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(18.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Initial Circle
            Surface(
                shape = androidx.compose.foundation.shape.CircleShape,
                color = bgColors[index],
                modifier = Modifier.size(52.dp)
            ) {
                Box(contentAlignment = Alignment.Center) {
                    Text(
                        text = initial,
                        fontFamily = PoppinsSemi,
                        fontSize = 20.sp,
                        color = textColors[index]
                    )
                }
            }

            Spacer(Modifier.width(16.dp))

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = item.nama,
                    fontFamily = PoppinsSemi,
                    fontSize = 15.sp,
                    color = TextPrimary
                )
                Spacer(Modifier.height(4.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(
                        imageVector = Icons.Default.Info,
                        contentDescription = null,
                        tint = BlueMain,
                        modifier = Modifier.size(14.dp)
                    )
                    Spacer(Modifier.width(6.dp))
                    Text(
                        text = item.alamat,
                        fontFamily = PoppinsReg,
                        fontSize = 13.sp,
                        color = TextMuted
                    )
                }
            }
            
            // Subtle arrow
            Icon(
                imageVector = Icons.Default.ChevronRight,
                contentDescription = null,
                tint = LineGray,
                modifier = Modifier.size(20.dp)
            )
        }
    }
}

@Preview(showBackground = true, showSystemUi = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewDirektori() {
    val context = androidx.compose.ui.platform.LocalContext.current
    val tokenStore = remember { TokenStore(context) }

    MaterialTheme {
        DirektoriScreen(tokenStore = tokenStore, onBack = {})
    }
}
