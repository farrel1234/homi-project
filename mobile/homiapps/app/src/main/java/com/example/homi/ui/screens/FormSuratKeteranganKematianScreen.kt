@file:OptIn(ExperimentalMaterial3Api::class, ExperimentalFoundationApi::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.ExperimentalFoundationApi
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.relocation.BringIntoViewRequester
import androidx.compose.foundation.relocation.bringIntoViewRequester
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.HistoryEdu
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.focus.onFocusEvent
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
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
import com.example.homi.data.repository.AccountRepository
import com.example.homi.data.remote.ApiClient
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import java.time.Instant
import java.time.ZoneId
import java.time.format.DateTimeFormatter

private val BlueMain = Color(0xFF2F7FA3)
private val BlueDark = Color(0xFF1A5E7B)
private val AccentOrange = Color(0xFFF7A477)
private val FieldBg = Color(0xFFF8FAFC)
private val FieldBorder = Color(0xFFE2E8F0)
private val TextDark = Color(0xFF1E293B)
private val HintGray = Color(0xFF94A3B8)
private val SuccessGreen = Color(0xFF22C55E)
private val ErrorRed = Color(0xFFEF4444)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

@Composable
fun FormSuratKeteranganKematianScreen(
    accountRepo: AccountRepository,
    onBack: () -> Unit = {},
    onKonfirmasi: (Map<String, String>) -> Unit = {}
) {
    val scope = rememberCoroutineScope()
    var isSelf by rememberSaveable { mutableStateOf(true) }
    var profileData by remember { mutableStateOf<com.example.homi.data.model.FullProfileResponse?>(null) }

    // Pelapor
    var namaPelapor by rememberSaveable { mutableStateOf("") }
    var nikPelapor by rememberSaveable { mutableStateOf("") }
    var alamatPelapor by rememberSaveable { mutableStateOf("") }
    var hubungan by rememberSaveable { mutableStateOf("") }

    // RT/RW/Nama RT (Pelapor)
    var rt by rememberSaveable { mutableStateOf("") }
    var rw by rememberSaveable { mutableStateOf("") }
    var namaRt by rememberSaveable { mutableStateOf("") }

    // Almarhum/Almarhumah
    var namaAlm by rememberSaveable { mutableStateOf("") }
    var nikAlm by rememberSaveable { mutableStateOf("") }
    var tempatLahirAlm by rememberSaveable { mutableStateOf("") }
    var tanggalLahirAlm by rememberSaveable { mutableStateOf("") }
    var alamatAlm by rememberSaveable { mutableStateOf("") }

    // Detail kematian
    var tanggalKematian by rememberSaveable { mutableStateOf("") }
    var tempatKematian by rememberSaveable { mutableStateOf("") }
    var penyebab by rememberSaveable { mutableStateOf("") }
    var keperluan by rememberSaveable { mutableStateOf("") }

    var showDatePickerBirth by remember { mutableStateOf(false) }
    var showDatePickerDeath by remember { mutableStateOf(false) }

    LaunchedEffect(Unit) {
        scope.launch {
            runCatching { accountRepo.getFullProfile() }
                .onSuccess { profile ->
                    profileData = profile
                    if (isSelf) {
                        namaPelapor = profile.fullName ?: profile.name ?: ""
                        nikPelapor = profile.residentProfile?.nik ?: ""
                        
                        rt = profile.residentProfile?.rt ?: ""
                        rw = profile.residentProfile?.rw ?: ""
                        namaRt = profile.residentProfile?.namaRt ?: ""

                        val blokStr = profile.residentProfile?.blok ?: ""
                        val noStr = profile.residentProfile?.noRumah ?: ""
                        if (blokStr.isNotBlank() || noStr.isNotBlank()) {
                            alamatPelapor = "Perumahan Hawai Garden Blok $blokStr No. $noStr".trim()
                        } else {
                            alamatPelapor = profile.residentProfile?.alamat ?: ""
                        }
                    }
                }
        }
    }

    LaunchedEffect(isSelf, profileData) {
        val p = profileData ?: return@LaunchedEffect
        if (isSelf) {
            namaPelapor = p.fullName ?: p.name ?: ""
            nikPelapor = p.residentProfile?.nik ?: ""
            rt = p.residentProfile?.rt ?: ""
            rw = p.residentProfile?.rw ?: ""
            namaRt = p.residentProfile?.namaRt ?: ""
        } else {
            namaPelapor = ""
            nikPelapor = ""
            rt = p.residentProfile?.rt ?: ""
            rw = p.residentProfile?.rw ?: ""
            namaRt = p.residentProfile?.namaRt ?: ""
        }
    }

    val canSubmit =
        namaPelapor.isNotBlank() && nikPelapor.isNotBlank() && alamatPelapor.isNotBlank() && 
        rt.isNotBlank() && rw.isNotBlank() && namaRt.isNotBlank() &&
        hubungan.isNotBlank() && namaAlm.isNotBlank() && nikAlm.isNotBlank() && 
        tempatLahirAlm.isNotBlank() && tanggalLahirAlm.isNotBlank() && 
        alamatAlm.isNotBlank() && tanggalKematian.isNotBlank() && 
        tempatKematian.isNotBlank() && penyebab.isNotBlank() && keperluan.isNotBlank()

    if (showDatePickerBirth) {
        val datePickerState = rememberDatePickerState()
        DatePickerDialog(
            onDismissRequest = { showDatePickerBirth = false },
            confirmButton = {
                TextButton(onClick = {
                    datePickerState.selectedDateMillis?.let {
                        val date = java.time.Instant.ofEpochMilli(it)
                            .atZone(java.time.ZoneId.systemDefault())
                            .toLocalDate()
                        tanggalLahirAlm = date.format(java.time.format.DateTimeFormatter.ISO_DATE)
                    }
                    showDatePickerBirth = false
                }) { Text("Pilih") }
            },
            dismissButton = { TextButton(onClick = { showDatePickerBirth = false }) { Text("Batal") } }
        ) { DatePicker(state = datePickerState) }
    }

    if (showDatePickerDeath) {
        val datePickerState = rememberDatePickerState()
        DatePickerDialog(
            onDismissRequest = { showDatePickerDeath = false },
            confirmButton = {
                TextButton(onClick = {
                    datePickerState.selectedDateMillis?.let {
                        val date = java.time.Instant.ofEpochMilli(it)
                            .atZone(java.time.ZoneId.systemDefault())
                            .toLocalDate()
                        tanggalKematian = date.format(java.time.format.DateTimeFormatter.ISO_DATE)
                    }
                    showDatePickerDeath = false
                }) { Text("Pilih") }
            },
            dismissButton = { TextButton(onClick = { showDatePickerDeath = false }) { Text("Batal") } }
        ) { DatePicker(state = datePickerState) }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Brush.verticalGradient(listOf(BlueMain, BlueDark)))
    ) {
        // ===== Header =====
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .statusBarsPadding()
                .padding(top = 16.dp, bottom = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Box(modifier = Modifier.fillMaxWidth().padding(horizontal = 8.dp)) {
                IconButton(
                    onClick = onBack,
                    modifier = Modifier.align(Alignment.CenterStart)
                ) {
                    Icon(
                        painter = painterResource(id = R.drawable.panahkembali),
                        contentDescription = "Kembali",
                        tint = Color.White,
                        modifier = Modifier.size(24.dp)
                    )
                }

                Text(
                    text = "Surat Kematian",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(12.dp))

            // Icon topper
            Box(
                modifier = Modifier
                    .size(56.dp)
                    .background(Color.White.copy(alpha = 0.15f), RoundedCornerShape(16.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Outlined.HistoryEdu,
                    contentDescription = null,
                    tint = Color.White,
                    modifier = Modifier.size(28.dp)
                )
            }

            Spacer(Modifier.height(8.dp))
            Text(
                text = "Lengkapi data pelapor dan almarhum\nuntuk pengajuan Surat Keterangan Kematian.",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                lineHeight = 18.sp
            )
        }

        // ===== Content Card =====
        Surface(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            color = Color.White
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .imePadding()
                    .navigationBarsPadding()
                    .padding(horizontal = 24.dp, vertical = 24.dp)
            ) {
                // Section 1: Pelapor
                Text(
                    text = "Pilih Pengaju",
                    fontFamily = PoppinsSemi,
                    fontSize = 15.sp,
                    color = BlueMain
                )

                Spacer(Modifier.height(12.dp))

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    SegmentedButton(
                        label = "Diri Sendiri",
                        selected = isSelf,
                        onClick = { isSelf = true },
                        modifier = Modifier.weight(1f)
                    )
                    SegmentedButton(
                        label = "Orang Lain",
                        selected = !isSelf,
                        onClick = { isSelf = false },
                        modifier = Modifier.weight(1f)
                    )
                }

                Spacer(Modifier.height(24.dp))

                Text("Data Pelapor", fontFamily = PoppinsSemi, fontSize = 15.sp, color = BlueMain)
                Spacer(Modifier.height(20.dp))
                HomiFormField("Nama Pelapor", "Nama lengkap Anda", namaPelapor, { namaPelapor = it }, enabled = !isSelf)
                Spacer(Modifier.height(16.dp))
                HomiFormField("NIK Pelapor", "16 digit NIK Anda", nikPelapor, { nikPelapor = it.filter { c -> c.isDigit() }.take(16) }, KeyboardType.Number, enabled = !isSelf)
                Spacer(Modifier.height(16.dp))
                HomiFormField("Alamat Pelapor", "Alamat domisili saat ini", alamatPelapor, { alamatPelapor = it }, singleLine = false, minLines = 2, enabled = false)
                
                Spacer(Modifier.height(16.dp))
                Row(modifier = Modifier.fillMaxWidth()) {
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField("RT", "001", rt, { rt = it }, KeyboardType.Number)
                    }
                    Spacer(Modifier.width(12.dp))
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField("RW", "002", rw, { rw = it }, KeyboardType.Number)
                    }
                }
                Spacer(Modifier.height(16.dp))
                HomiFormField("Nama Ketua RT", "Masukkan Nama Ketua RT", namaRt, { namaRt = it })
                
                Spacer(Modifier.height(16.dp))
                HomiFormField("Hubungan dengan Almarhum", "Anak / Istri / Suami / Keluarga", hubungan, { hubungan = it })

                Spacer(Modifier.height(24.dp))
                HorizontalDivider(color = Color(0xFFF1F5F9), thickness = 1.dp)
                Spacer(Modifier.height(20.dp))

                // Section 2: Almarhum
                Text("Data Almarhum/Almarhumah", fontFamily = PoppinsSemi, fontSize = 15.sp, color = BlueMain)
                Spacer(Modifier.height(20.dp))
                HomiFormField("Nama Almarhum/ah", "Sesuai KTP/KK", namaAlm, { namaAlm = it })
                Spacer(Modifier.height(16.dp))
                HomiFormField("NIK Almarhum/ah", "16 digit NIK", nikAlm, { nikAlm = it.filter { c -> c.isDigit() }.take(16) }, KeyboardType.Number)
                Spacer(Modifier.height(16.dp))
                
                Row(modifier = Modifier.fillMaxWidth()) {
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField("Tempat Lahir", "Batam", tempatLahirAlm, { tempatLahirAlm = it })
                    }
                    Spacer(Modifier.width(12.dp))
                    Box(modifier = Modifier.weight(1f)) {
                        Column {
                            Text("Tanggal Lahir", fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextDark)
                            Spacer(Modifier.height(6.dp))
                            Surface(
                                modifier = Modifier.fillMaxWidth().height(56.dp).clickable { showDatePickerBirth = true },
                                shape = RoundedCornerShape(14.dp), color = FieldBg, border = BorderStroke(1.dp, FieldBorder)
                            ) {
                                Box(contentAlignment = Alignment.CenterStart, modifier = Modifier.padding(horizontal = 16.dp)) {
                                    Text(text = tanggalLahirAlm.ifBlank { "Pilih Tanggal" }, color = if (tanggalLahirAlm.isBlank()) HintGray else TextDark, fontFamily = PoppinsReg, fontSize = 14.sp)
                                }
                            }
                        }
                    }
                }
                Spacer(Modifier.height(16.dp))
                HomiFormField("Alamat Terakhir", "Sesuai KTP/KK almarhum", alamatAlm, { alamatAlm = it }, singleLine = false, minLines = 2)

                Spacer(Modifier.height(24.dp))
                HorizontalDivider(color = Color(0xFFF1F5F9), thickness = 1.dp)
                Spacer(Modifier.height(20.dp))

                // Section 3: Detail Kematian
                Text("Detail Kematian", fontFamily = PoppinsSemi, fontSize = 15.sp, color = BlueMain)
                Spacer(Modifier.height(20.dp))
                Column {
                    Text("Tanggal Meninggal Dunia", fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextDark)
                    Spacer(Modifier.height(6.dp))
                    Surface(
                        modifier = Modifier.fillMaxWidth().height(56.dp).clickable { showDatePickerDeath = true },
                        shape = RoundedCornerShape(14.dp), color = FieldBg, border = BorderStroke(1.dp, FieldBorder)
                    ) {
                        Box(contentAlignment = Alignment.CenterStart, modifier = Modifier.padding(horizontal = 16.dp)) {
                            Text(text = tanggalKematian.ifBlank { "Pilih Tanggal" }, color = if (tanggalKematian.isBlank()) HintGray else TextDark, fontFamily = PoppinsReg, fontSize = 14.sp)
                        }
                    }
                }
                Spacer(Modifier.height(16.dp))
                HomiFormField("Tempat Meninggal Dunia", "Contoh: RS. Harapan / Rumah", tempatKematian, { tempatKematian = it })
                Spacer(Modifier.height(16.dp))
                HomiFormField("Penyebab Kematian", "Sakit / Kecelakaan / Lainnya", penyebab, { penyebab = it })
                Spacer(Modifier.height(16.dp))
                HomiFormField("Keperluan Surat", "Administrasi kependudukan / Waris", keperluan, { keperluan = it }, singleLine = false, minLines = 2)

                Spacer(Modifier.height(32.dp))

                Button(
                    onClick = {
                        val payload = mutableMapOf(
                            "pengaju" to (if (isSelf) "Diri Sendiri" else "Orang Lain"),
                            "nama_pelapor" to namaPelapor.trim(),
                            "nik_pelapor" to nikPelapor.trim(),
                            "alamat_pelapor" to alamatPelapor.trim(),
                            "rt" to rt.trim(),
                            "rw" to rw.trim(),
                            "nama_rt" to namaRt.trim(),
                            "hubungan" to hubungan.trim(),
                            "nama_alm" to namaAlm.trim(),
                            "nik_alm" to nikAlm.trim(),
                            "tempat_lahir_alm" to tempatLahirAlm.trim(),
                            "tanggal_lahir_alm" to tanggalLahirAlm.trim(),
                            "alamat_alm" to alamatAlm.trim(),
                            "tanggal_kematian" to tanggalKematian.trim(),
                            "tempat_kematian" to tempatKematian.trim(),
                            "penyebab" to penyebab.trim(),
                            "keperluan" to keperluan.trim()
                        )
                        onKonfirmasi(payload)
                    },
                    enabled = canSubmit,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = AccentOrange,
                        disabledContainerColor = AccentOrange.copy(alpha = 0.45f)
                    ),
                    shape = RoundedCornerShape(16.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(52.dp),
                    elevation = ButtonDefaults.buttonElevation(defaultElevation = 4.dp)
                ) {
                    Text(
                        text = "Konfirmasi Data Kematian",
                        fontFamily = PoppinsSemi,
                        color = Color.White,
                        fontSize = 15.sp
                    )
                }

                Spacer(Modifier.height(12.dp))

                Text(
                    text = if (canSubmit) "✓ Semua data kematian sudah lengkap." else "✕ Harap lengkapi semua data formulir.",
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = if (canSubmit) SuccessGreen else ErrorRed,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center
                )

                Spacer(Modifier.height(24.dp))
            }
        }
    }
}

@OptIn(ExperimentalFoundationApi::class)
@Composable
private fun HomiFormField(
    label: String,
    placeholder: String,
    value: String,
    onValueChange: (String) -> Unit,
    keyboardType: KeyboardType = KeyboardType.Text,
    singleLine: Boolean = true,
    minLines: Int = 1,
    enabled: Boolean = true
) {
    val bringIntoViewRequester = remember { BringIntoViewRequester() }
    val scope = rememberCoroutineScope()

    Column(modifier = Modifier.fillMaxWidth()) {
        Text(
            text = label,
            fontFamily = PoppinsSemi,
            color = if (enabled) TextDark else HintGray,
            fontSize = 13.sp,
            modifier = Modifier.padding(bottom = 6.dp)
        )

        OutlinedTextField(
            value = value,
            onValueChange = onValueChange,
            enabled = enabled,
            placeholder = {
                Text(
                    text = placeholder,
                    fontFamily = PoppinsReg,
                    fontSize = 14.sp,
                    color = HintGray
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
                            delay(200)
                            bringIntoViewRequester.bringIntoView()
                        }
                    }
                },
            shape = RoundedCornerShape(14.dp),
            textStyle = androidx.compose.ui.text.TextStyle(
                fontFamily = PoppinsReg,
                fontSize = 14.sp,
                color = TextDark
            ),
            colors = OutlinedTextFieldDefaults.colors(
                focusedBorderColor = BlueMain,
                unfocusedBorderColor = FieldBorder,
                focusedContainerColor = FieldBg,
                unfocusedContainerColor = FieldBg,
                cursorColor = BlueMain
            )
        )
    }
}

@Composable
private fun SegmentedButton(
    label: String,
    selected: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Button(
        onClick = onClick,
        modifier = modifier.height(44.dp),
        shape = RoundedCornerShape(12.dp),
        colors = ButtonDefaults.buttonColors(
            containerColor = if (selected) BlueMain else FieldBg,
            contentColor = if (selected) Color.White else TextDark
        ),
        elevation = ButtonDefaults.buttonElevation(defaultElevation = if (selected) 2.dp else 0.dp),
        border = if (selected) null else BorderStroke(1.dp, FieldBorder)
    ) {
        Text(label, fontFamily = PoppinsSemi, fontSize = 13.sp)
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewFormSuratKematianPremium() {
    MaterialTheme {
        val mockRepo = AccountRepository(ApiClient.getApiMock())
        FormSuratKeteranganKematianScreen(accountRepo = mockRepo)
    }
}
