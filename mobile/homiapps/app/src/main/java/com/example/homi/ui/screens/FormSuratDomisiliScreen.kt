@file:OptIn(ExperimentalMaterial3Api::class, ExperimentalFoundationApi::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.ExperimentalFoundationApi
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.relocation.BringIntoViewRequester
import androidx.compose.foundation.relocation.bringIntoViewRequester
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Description
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
fun FormSuratDomisiliScreen(
    accountRepo: AccountRepository,
    onBack: () -> Unit = {},
    onKonfirmasi: (Map<String, String>) -> Unit = {}
) {
    val scope = rememberCoroutineScope()

    var isSelf by rememberSaveable { mutableStateOf(true) }
    var profileData by remember { mutableStateOf<com.example.homi.data.model.FullProfileResponse?>(null) }

    var nama by rememberSaveable { mutableStateOf("") }
    var nik by rememberSaveable { mutableStateOf("") }
    var alamat by rememberSaveable { mutableStateOf("") }
    var blok by rememberSaveable { mutableStateOf("") }
    var noRumah by rememberSaveable { mutableStateOf("") }
    var rt by rememberSaveable { mutableStateOf("") }
    var rw by rememberSaveable { mutableStateOf("") }
    var namaRt by rememberSaveable { mutableStateOf("") }
    var tempatLahir by rememberSaveable { mutableStateOf("") }
    var tanggalLahir by rememberSaveable { mutableStateOf("") }
    var jenisKelamin by rememberSaveable { mutableStateOf("") }
    var keperluan by rememberSaveable { mutableStateOf("") }

    val datePickerState = rememberDatePickerState()
    var showDatePicker by remember { mutableStateOf(false) }

    // FETCH PROFILE
    LaunchedEffect(Unit) {
        scope.launch {
            runCatching { accountRepo.getFullProfile() }
                .onSuccess { profile ->
                    profileData = profile
                    val rp = profile.residentProfile
                    
                    // Always set block and house number even if not self (usually same household)
                    blok = rp?.blok ?: ""
                    noRumah = rp?.noRumah ?: ""
                    val b = rp?.blok ?: ""
                    val n = rp?.noRumah ?: ""
                    alamat = "Perumahan Hawai Garden Blok $b No. $n".trim()
                    
                    if (isSelf) {
                        val raw = profile.fullName ?: profile.name ?: ""
                        nama = if (raw.lowercase().contains("warga")) "" else raw
                        
                        nik = rp?.nik ?: ""
                        rt = rp?.rt ?: ""
                        rw = rp?.rw ?: ""
                        namaRt = rp?.namaRt ?: ""
                        tempatLahir = rp?.tempatLahir ?: ""
                        tanggalLahir = rp?.tanggalLahir ?: ""
                        jenisKelamin = rp?.jenisKelamin ?: ""
                    }
                }
        }
    }

    LaunchedEffect(isSelf, profileData) {
        val p = profileData ?: return@LaunchedEffect
        val rp = p.residentProfile
        if (isSelf) {
            val raw = p.fullName ?: p.name ?: ""
            nama = if (raw.lowercase().contains("warga")) "" else raw
            
            nik = rp?.nik ?: ""
            rt = rp?.rt ?: ""
            rw = rp?.rw ?: ""
            namaRt = rp?.namaRt ?: ""
            tempatLahir = rp?.tempatLahir ?: ""
            tanggalLahir = rp?.tanggalLahir ?: ""
            jenisKelamin = rp?.jenisKelamin ?: ""
            
            val b = rp?.blok ?: ""
            val n = rp?.noRumah ?: ""
            alamat = "Perumahan Hawai Garden Blok $b No. $n".trim()
        } else {
            // Reset fields for "Orang Lain"
            nama = ""
            nik = ""
            // RT/RW/NamaRT typically stay same if living together, but let user edit
            rt = rp?.rt ?: ""
            rw = rp?.rw ?: ""
            namaRt = rp?.namaRt ?: ""
            
            tempatLahir = ""
            tanggalLahir = ""
            jenisKelamin = ""
            
            val b = rp?.blok ?: ""
            val n = rp?.noRumah ?: ""
            alamat = "Perumahan Hawai Garden Blok $b No. $n".trim()
        }
    }

    if (showDatePicker) {
        DatePickerDialog(
            onDismissRequest = { showDatePicker = false },
            confirmButton = {
                TextButton(onClick = {
                    val date = datePickerState.selectedDateMillis?.let {
                        java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.getDefault()).format(java.util.Date(it))
                    } ?: ""
                    tanggalLahir = date
                    showDatePicker = false
                }) {
                    Text("OK", fontFamily = PoppinsSemi)
                }
            },
            dismissButton = {
                TextButton(onClick = { showDatePicker = false }) {
                    Text("Batal", fontFamily = PoppinsSemi)
                }
            }
        ) {
            DatePicker(state = datePickerState)
        }
    }

    val canSubmit = nama.isNotBlank() && nik.isNotBlank() && 
                    rt.isNotBlank() && rw.isNotBlank() && namaRt.isNotBlank() &&
                    tempatLahir.isNotBlank() && tanggalLahir.isNotBlank() && 
                    keperluan.isNotBlank()

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
                .padding(top = 16.dp, bottom = 12.dp),
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
                    text = "Surat Domisili",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(12.dp))
            Text(
                text = "Lengkapi data di bawah ini untuk\npengajuan Surat Keterangan Domisili.",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                lineHeight = 16.sp
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

                Text(
                    text = "Data Pemohon",
                    fontFamily = PoppinsSemi,
                    fontSize = 15.sp,
                    color = BlueMain
                )

                Spacer(Modifier.height(20.dp))

                HomiFormField(
                    label = "Nama Lengkap",
                    placeholder = "Misal: Budi Santoso",
                    value = nama,
                    onValueChange = { nama = it },
                    enabled = !isSelf || nama.isBlank()
                )

                Spacer(Modifier.height(16.dp))

                HomiFormField(
                    label = "NIK",
                    placeholder = "Masukkan 16 digit NIK",
                    value = nik,
                    onValueChange = { nik = it.filter { c -> c.isDigit() }.take(16) },
                    keyboardType = KeyboardType.Number,
                    enabled = !isSelf
                )

                Spacer(Modifier.height(16.dp))

                Row(Modifier.fillMaxWidth()) {
                    Box(Modifier.weight(1f)) {
                        HomiFormField(
                            label = "Tempat Lahir",
                            placeholder = "Misal: Jakarta",
                            value = tempatLahir,
                            onValueChange = { tempatLahir = it },
                            enabled = !isSelf
                        )
                    }
                    Spacer(Modifier.width(12.dp))
                    Box(Modifier.weight(1f)) {
                        Column {
                            Text("Tanggal Lahir", fontFamily = PoppinsSemi, fontSize = 13.sp, color = if (!isSelf) TextDark else HintGray)
                            Spacer(Modifier.height(6.dp))
                            OutlinedTextField(
                                value = tanggalLahir,
                                onValueChange = {},
                                readOnly = true,
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .clickable { if (!isSelf) showDatePicker = true },
                                enabled = false,
                                shape = RoundedCornerShape(14.dp),
                                textStyle = androidx.compose.ui.text.TextStyle(fontFamily = PoppinsReg, fontSize = 14.sp, color = if (!isSelf) TextDark else HintGray),
                                placeholder = { Text("YYYY-MM-DD", fontSize = 14.sp, color = HintGray) },
                                colors = OutlinedTextFieldDefaults.colors(
                                    disabledTextColor = if (!isSelf) TextDark else HintGray,
                                    disabledBorderColor = FieldBorder,
                                    disabledContainerColor = FieldBg
                                ),
                                trailingIcon = {
                                    Icon(
                                        painter = painterResource(R.drawable.ic_calendar),
                                        contentDescription = null,
                                        tint = if (!isSelf) BlueMain else HintGray,
                                        modifier = Modifier.size(20.dp).clickable { if (!isSelf) showDatePicker = true }
                                    )
                                }
                            )
                        }
                    }
                }

                Spacer(Modifier.height(16.dp))

                Text("Jenis Kelamin", fontFamily = PoppinsSemi, fontSize = 13.sp, color = if (!isSelf) TextDark else HintGray)
                Row(verticalAlignment = Alignment.CenterVertically) {
                    RadioButton(
                        selected = jenisKelamin == "Laki-laki", 
                        onClick = { if (!isSelf) jenisKelamin = "Laki-laki" },
                        enabled = !isSelf
                    )
                    Text("Laki-laki", fontSize = 14.sp, color = if (!isSelf) TextDark else HintGray)
                    Spacer(Modifier.width(16.dp))
                    RadioButton(
                        selected = jenisKelamin == "Perempuan", 
                        onClick = { if (!isSelf) jenisKelamin = "Perempuan" },
                        enabled = !isSelf
                    )
                    Text("Perempuan", fontSize = 14.sp, color = if (!isSelf) TextDark else HintGray)
                }

                Spacer(Modifier.height(16.dp))

                HomiFormField(
                    label = "Alamat Lengkap",
                    placeholder = "Alamat sesuai KTP/KK",
                    value = alamat,
                    onValueChange = { alamat = it },
                    singleLine = false,
                    minLines = 2,
                    enabled = false // Selalu locked
                )

                Spacer(Modifier.height(16.dp))

                Row(modifier = Modifier.fillMaxWidth()) {
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField(
                            label = "Blok",
                            placeholder = "Misal: A",
                            value = blok,
                            onValueChange = { blok = it },
                            enabled = false // Selalu locked
                        )
                    }
                    Spacer(Modifier.width(16.dp))
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField(
                            label = "No. Rumah",
                            placeholder = "Misal: 12",
                            value = noRumah,
                            onValueChange = { noRumah = it },
                            enabled = false // Selalu locked
                        )
                    }
                }

                Spacer(Modifier.height(16.dp))

                Row(modifier = Modifier.fillMaxWidth()) {
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField(
                            label = "RT",
                            placeholder = "001",
                            value = rt,
                            onValueChange = { rt = it },
                            keyboardType = KeyboardType.Number
                        )
                    }
                    Spacer(Modifier.width(16.dp))
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField(
                            label = "RW",
                            placeholder = "002",
                            value = rw,
                            onValueChange = { rw = it },
                            keyboardType = KeyboardType.Number
                        )
                    }
                }

                Spacer(Modifier.height(16.dp))

                HomiFormField(
                    label = "Nama Ketua RT",
                    placeholder = "Nama Ketua RT Anda",
                    value = namaRt,
                    onValueChange = { namaRt = it }
                )

                Spacer(Modifier.height(16.dp))

                HomiFormField(
                    label = "Keperluan",
                    placeholder = "Misal: Pengurusan Administrasi Bank",
                    value = keperluan,
                    onValueChange = { keperluan = it },
                    singleLine = false,
                    minLines = 2
                )

                Spacer(Modifier.height(32.dp))

                Button(
                    onClick = {
                        val payload = mapOf(
                            "pengaju" to (if (isSelf) "Diri Sendiri" else "Orang Lain"),
                            "nama" to nama.trim(),
                            "nik" to nik.trim(),
                            "tempat_lahir" to tempatLahir.trim(),
                            "tanggal_lahir" to tanggalLahir.trim(),
                            "jenis_kelamin" to jenisKelamin.trim(),
                            "alamat" to alamat.trim(),
                            "blok" to blok.trim(),
                            "no_rumah" to noRumah.trim(),
                            "rt" to rt.trim(),
                            "rw" to rw.trim(),
                            "nama_rt" to namaRt.trim(),
                            "keperluan" to keperluan.trim(),
                            "pj" to "KETUA RT"
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
                        text = "Konfirmasi Data",
                        fontFamily = PoppinsSemi,
                        color = Color.White,
                        fontSize = 15.sp
                    )
                }

                Spacer(Modifier.height(12.dp))

                Text(
                    text = if (canSubmit) "✓ Data sudah lengkap." else "✕ Mohon lengkapi semua field bertanda.",
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
private fun PreviewFormSuratDomisiliPremium() {
    MaterialTheme {
        val mockRepo = AccountRepository(ApiClient.getApiMock())
        FormSuratDomisiliScreen(accountRepo = mockRepo)
    }
}
