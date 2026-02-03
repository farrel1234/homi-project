// File: app/src/main/java/com/example/homi/ui/screens/FormLaporan.kt
package com.example.homi.ui.screens

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.imePadding
import androidx.compose.foundation.layout.navigationBarsPadding
import androidx.compose.foundation.layout.statusBarsPadding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Close
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.repository.ComplaintRepository
import kotlinx.coroutines.launch

private val BlueMain = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFE26A2C)
private val LineGray = Color(0xFFE5E7EB)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

@Composable
fun FormLaporanScreen(
    complaintRepo: ComplaintRepository,
    onBack: () -> Unit,
    onCreated: (Long) -> Unit
) {
    val ctx = LocalContext.current
    val scope = rememberCoroutineScope()

    var nama by remember { mutableStateOf("") }
    var tempat by remember { mutableStateOf("") }
    var perihal by remember { mutableStateOf("") }

    // ✅ tanggal angka saja: ddMMyyyy
    var tanggalInput by remember { mutableStateOf("") }

    // ✅ foto opsional
    var fotoUri by remember { mutableStateOf<Uri?>(null) }
    val pickImage = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        fotoUri = uri
    }

    var loading by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }

    var showSuccess by remember { mutableStateOf(false) }

    fun validDdMmYyyy(raw: String): Boolean {
        if (raw.length != 8) return false
        if (!raw.all { it.isDigit() }) return false
        val dd = raw.substring(0, 2).toIntOrNull() ?: return false
        val mm = raw.substring(2, 4).toIntOrNull() ?: return false
        val yyyy = raw.substring(4, 8).toIntOrNull() ?: return false
        if (yyyy < 1900) return false
        if (mm !in 1..12) return false
        if (dd !in 1..31) return false
        return true
    }

    fun submit() {
        val n = nama.trim()
        val t = tempat.trim()
        val p = perihal.trim()
        val d = tanggalInput.trim()

        if (n.isBlank() || t.isBlank() || p.isBlank() || d.isBlank()) {
            error = "Lengkapi semua field wajib."
            return
        }
        if (!validDdMmYyyy(d)) {
            error = "Format tanggal harus ddMMyyyy (contoh: 09012026)."
            return
        }

        loading = true
        error = null

        scope.launch {
            try {
                val created = complaintRepo.create(
                    context = ctx,
                    namaPelapor = n,
                    tanggalInputDdmmyyyy = d,
                    tempatKejadian = t,
                    perihal = p,
                    fotoUri = fotoUri
                )

                val id = created?.id ?: throw Exception("Gagal membuat laporan (id null)")
                showSuccess = true

                // langsung ke stepper by id
                onCreated(id)
            } catch (e: Exception) {
                error = e.message ?: "Gagal mengirim laporan"
            } finally {
                loading = false
            }
        }
    }

    // ✅ Scroll + nyaman saat keyboard muncul:
    // - Card content dibuat verticalScroll
    // - bagian bawah pakai imePadding + navigationBarsPadding
    // - tombol KONFIRMASI tetap di bawah dan ikut naik saat keyboard muncul
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        // ===== HEADER (biru) =====
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 12.dp)
        ) {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(44.dp)
            ) {
                Icon(
                    imageVector = Icons.Default.ArrowBack,
                    contentDescription = "Back",
                    tint = Color.White,
                    modifier = Modifier
                        .align(Alignment.CenterStart)
                        .size(22.dp)
                        .clickable { onBack() }
                )

                Text(
                    text = "Formulir Pengaduan",
                    fontFamily = PoppinsSemi,
                    fontSize = 16.sp,
                    color = Color.White,
                    textAlign = TextAlign.Center,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(6.dp))

            Text(
                text = "Untuk melaporkan masalah di area lingkungan Anda,\nsilahkan mengisi data formulir dibawah ini:",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.92f),
                modifier = Modifier.fillMaxWidth(),
                textAlign = TextAlign.Start,
                lineHeight = 16.sp
            )

            Spacer(Modifier.height(10.dp))
        }

        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 28.dp, topEnd = 28.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            val scrollState = rememberScrollState()

            // ✅ tombol tetap di bawah, konten di atas bisa scroll
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .imePadding()              // dorong konten & tombol naik saat keyboard muncul
                    .navigationBarsPadding()   // aman untuk gesture navigation
                    .padding(16.dp)
            ) {
                Text(
                    text = "Silahkan isi data laporan di bawah ini",
                    fontFamily = PoppinsSemi,
                    fontSize = 13.sp,
                    color = AccentOrange,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center
                )

                Spacer(Modifier.height(14.dp))

                // ===== FORM (SCROLL AREA) =====
                Column(
                    modifier = Modifier
                        .weight(1f, fill = true)
                        .verticalScroll(scrollState)
                        .padding(bottom = 12.dp) // jarak aman sebelum tombol
                ) {
                    // Nama
                    Field(
                        label = "Nama",
                        value = nama,
                        onValueChange = { nama = it },
                        placeholder = "Masukkan nama pelapor",
                        keyboardType = KeyboardType.Text,
                        imeAction = ImeAction.Next,
                        onImeAction = {}
                    )

                    // Tanggal (ddMMyyyy)
                    Field(
                        label = "Tanggal Pengaduan",
                        value = tanggalInput,
                        onValueChange = { input ->
                            val filtered = input.filter { it.isDigit() }.take(8)
                            tanggalInput = filtered
                        },
                        placeholder = "ddMMyyyy (contoh: 09012026)",
                        keyboardType = KeyboardType.Number,
                        imeAction = ImeAction.Next,
                        onImeAction = {}
                    )

                    // Tempat
                    Field(
                        label = "Tempat Kejadian",
                        value = tempat,
                        onValueChange = { tempat = it },
                        placeholder = "Contoh: Blok A / Pos Satpam",
                        keyboardType = KeyboardType.Text,
                        imeAction = ImeAction.Next,
                        onImeAction = {}
                    )

                    // Perihal
                    Field(
                        label = "Perihal",
                        value = perihal,
                        onValueChange = { perihal = it },
                        placeholder = "Contoh: Sampah berserakan / Lampu mati",
                        keyboardType = KeyboardType.Text,
                        imeAction = ImeAction.Done,
                        onImeAction = { if (!loading) submit() }
                    )

                    Spacer(Modifier.height(10.dp))

                    // Upload foto opsional
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(14.dp),
                        border = BorderStroke(1.dp, LineGray),
                        colors = CardDefaults.cardColors(containerColor = Color(0xFFF9FAFB)),
                        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                    ) {
                        Column(Modifier.padding(12.dp)) {
                            Text(
                                text = "Foto (Opsional)",
                                fontFamily = PoppinsSemi,
                                fontSize = 12.sp,
                                color = Color(0xFF0E0E0E)
                            )

                            Spacer(Modifier.height(6.dp))

                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                OutlinedButton(
                                    onClick = { pickImage.launch("image/*") },
                                    shape = RoundedCornerShape(12.dp),
                                    border = BorderStroke(1.dp, BlueMain),
                                    modifier = Modifier.height(42.dp)
                                ) {
                                    Text(
                                        "Upload Foto",
                                        fontFamily = PoppinsSemi,
                                        fontSize = 12.sp,
                                        color = BlueMain
                                    )
                                }

                                Spacer(Modifier.width(10.dp))

                                if (fotoUri != null) {
                                    Text(
                                        text = "Foto dipilih",
                                        fontFamily = PoppinsReg,
                                        fontSize = 12.sp,
                                        color = Color(0xFF64748B),
                                        modifier = Modifier.weight(1f)
                                    )
                                    Icon(
                                        imageVector = Icons.Default.Close,
                                        contentDescription = "Hapus",
                                        tint = Color(0xFF64748B),
                                        modifier = Modifier
                                            .size(18.dp)
                                            .clickable { fotoUri = null }
                                    )
                                } else {
                                    Text(
                                        text = "Tidak ada foto",
                                        fontFamily = PoppinsReg,
                                        fontSize = 12.sp,
                                        color = Color(0xFF94A3B8)
                                    )
                                }
                            }
                        }
                    }

                    Spacer(Modifier.height(12.dp))

                    if (error != null) {
                        Text(
                            text = error!!,
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = Color(0xFFDC2626)
                        )
                        Spacer(Modifier.height(8.dp))
                    }

                    // spacer kecil agar scroll enak
                    Spacer(Modifier.height(8.dp))
                }

                // ===== FOOTER BUTTON (STICKY) =====
                Button(
                    onClick = { if (!loading) submit() },
                    enabled = !loading,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp),
                    shape = RoundedCornerShape(14.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = AccentOrange)
                ) {
                    if (loading) {
                        CircularProgressIndicator(
                            modifier = Modifier.size(18.dp),
                            strokeWidth = 2.dp,
                            color = Color.White
                        )
                        Spacer(Modifier.width(10.dp))
                        Text(
                            text = "Mengirim...",
                            fontFamily = PoppinsSemi,
                            fontSize = 14.sp,
                            color = Color.White
                        )
                    } else {
                        Text(
                            text = "Konfirmasi",
                            fontFamily = PoppinsSemi,
                            fontSize = 14.sp,
                            color = Color.White
                        )
                    }
                }

                Spacer(Modifier.height(10.dp))
            }
        }
    }

    if (showSuccess) {
        AlertDialog(
            onDismissRequest = { showSuccess = false },
            confirmButton = {
                TextButton(onClick = { showSuccess = false }) {
                    Text("OK", fontFamily = PoppinsSemi, color = BlueMain)
                }
            },
            title = { Text("Berhasil", fontFamily = PoppinsSemi) },
            text = { Text("Laporan kamu sudah terkirim.", fontFamily = PoppinsReg, fontSize = 13.sp) }
        )
    }
}

@Composable
private fun Field(
    label: String,
    value: String,
    onValueChange: (String) -> Unit,
    placeholder: String,
    keyboardType: KeyboardType = KeyboardType.Text,
    imeAction: ImeAction = ImeAction.Next,
    onImeAction: () -> Unit = {}
) {
    Column {
        Text(
            text = label,
            fontFamily = PoppinsSemi,
            fontSize = 12.sp,
            color = Color(0xFF0E0E0E)
        )
        Spacer(Modifier.height(6.dp))
        OutlinedTextField(
            value = value,
            onValueChange = onValueChange,
            placeholder = { Text(placeholder, fontFamily = PoppinsReg, fontSize = 12.sp) },
            textStyle = LocalTextStyle.current.copy(fontFamily = PoppinsReg, fontSize = 12.sp),
            keyboardOptions = KeyboardOptions(
                keyboardType = keyboardType,
                imeAction = imeAction
            ),
            keyboardActions = KeyboardActions(
                onNext = { onImeAction() },
                onDone = { onImeAction() }
            ),
            singleLine = true,
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(14.dp)
        )
        Spacer(Modifier.height(10.dp))
    }
}
