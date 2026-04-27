package com.example.homi.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Phone
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.LocationOn
import androidx.compose.material.icons.filled.CalendarToday
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.ui.viewmodel.ProfileViewModel
import java.text.SimpleDateFormat
import java.util.*

private val BlueMain = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CompleteProfileScreen(
    vm: ProfileViewModel,
    onBack: () -> Unit,
    onSuccess: () -> Unit
) {
    val state by vm.state.collectAsState()
    val scrollState = rememberScrollState()

    var fullName by remember { mutableStateOf("") }
    var phone by remember { mutableStateOf("") }
    var nik by remember { mutableStateOf("") }
    var blok by remember { mutableStateOf("") }
    var noRumah by remember { mutableStateOf("") }
    var rt by remember { mutableStateOf("") }
    var rw by remember { mutableStateOf("") }
    var namaRt by remember { mutableStateOf("") }
    var pekerjaan by remember { mutableStateOf("") }
    var tempatLahir by remember { mutableStateOf("") }
    var tanggalLahir by remember { mutableStateOf("") }
    var houseType by remember { mutableStateOf("") }
    var jenisKelamin by remember { mutableStateOf("") }

    val datePickerState = rememberDatePickerState()
    var showDatePicker by remember { mutableStateOf(false) }

    val houseTypes = listOf("Tipe 36", "Tipe 45", "Tipe 60", "Tipe 72")

    LaunchedEffect(Unit) {
        vm.loadProfile()
    }

    LaunchedEffect(state.profile) {
        state.profile?.let { p ->
            fullName = p.fullName ?: ""
            phone = p.phone ?: ""
            p.residentProfile?.let { rp ->
                nik = rp.nik ?: ""
                blok = rp.blok ?: ""
                noRumah = rp.noRumah ?: ""
                rt = rp.rt ?: ""
                rw = rp.rw ?: ""
                namaRt = rp.namaRt ?: ""
                pekerjaan = rp.pekerjaan ?: ""
                houseType = rp.houseType ?: ""
                jenisKelamin = rp.jenisKelamin ?: ""
                tempatLahir = rp.tempatLahir ?: ""
                tanggalLahir = rp.tanggalLahir ?: ""
            }
        }
    }

    LaunchedEffect(state.success) {
        if (state.success) {
            onSuccess()
            vm.resetState()
        }
    }

    if (showDatePicker) {
        DatePickerDialog(
            onDismissRequest = { showDatePicker = false },
            confirmButton = {
                TextButton(onClick = {
                    val date = datePickerState.selectedDateMillis?.let {
                        SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date(it))
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

    Scaffold(
        topBar = {
            CenterAlignedTopAppBar(
                title = {
                    Text(
                        "Lengkapi Profil",
                        fontFamily = PoppinsSemi,
                        fontSize = 18.sp,
                        color = Color.White
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Kembali", tint = Color.White)
                    }
                },
                colors = TopAppBarDefaults.centerAlignedTopAppBarColors(containerColor = BlueMain)
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(BlueMain)
                .padding(padding)
        ) {
            Surface(
                modifier = Modifier.fillMaxSize(),
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                color = Color.White
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(24.dp)
                        .verticalScroll(scrollState)
                ) {
                    Text(
                        "Mohon lengkapi data diri Anda untuk keperluan administrasi perumahan.",
                        fontFamily = PoppinsReg,
                        fontSize = 14.sp,
                        color = Color.Gray,
                        modifier = Modifier.padding(bottom = 24.dp)
                    )

                    ProfileTextField("Nama Lengkap", fullName, Icons.Default.Person) { fullName = it }
                    Spacer(Modifier.height(16.dp))

                    ProfileTextField("Nomor Telepon", phone, Icons.Default.Phone) { phone = it }
                    Spacer(Modifier.height(16.dp))

                    Row(Modifier.fillMaxWidth()) {
                        Box(Modifier.weight(1f)) {
                            ProfileTextField("Blok", blok, Icons.Default.Home) { blok = it }
                        }
                        Spacer(Modifier.width(16.dp))
                        Box(Modifier.weight(1f)) {
                            ProfileTextField("No. Rumah", noRumah, Icons.Default.Home) { noRumah = it }
                        }
                    }
                    Spacer(Modifier.height(16.dp))

                    Row(Modifier.fillMaxWidth()) {
                        Box(Modifier.weight(1f)) {
                            ProfileTextField("RT", rt, Icons.Default.Home) { rt = it }
                        }
                        Spacer(Modifier.width(16.dp))
                        Box(Modifier.weight(1f)) {
                            ProfileTextField("RW", rw, Icons.Default.Home) { rw = it }
                        }
                    }
                    Spacer(Modifier.height(16.dp))
                    
                    ProfileTextField("Nama Ketua RT", namaRt, Icons.Default.Person) { namaRt = it }
                    Spacer(Modifier.height(16.dp))

                    // Tipe Rumah Dropdown
                    Text("Tipe Rumah", fontFamily = PoppinsSemi, fontSize = 13.sp, color = BlueMain)
                    Spacer(Modifier.height(8.dp))
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        houseTypes.forEach { type ->
                            FilterChip(
                                selected = houseType == type,
                                onClick = { houseType = type },
                                label = { Text(type, fontFamily = PoppinsReg, fontSize = 12.sp) },
                                colors = FilterChipDefaults.filterChipColors(
                                    selectedContainerColor = BlueMain,
                                    selectedLabelColor = Color.White
                                )
                            )
                        }
                    }
                    Spacer(Modifier.height(16.dp))

                    ProfileTextField("NIK (16 Digit)", nik, Icons.Default.Person) { nik = it.filter { c -> c.isDigit() }.take(16) }
                    Spacer(Modifier.height(16.dp))

                    Row(Modifier.fillMaxWidth()) {
                        Box(Modifier.weight(1f)) {
                            ProfileTextField("Tempat Lahir", tempatLahir, Icons.Default.LocationOn) { tempatLahir = it }
                        }
                        Spacer(Modifier.width(16.dp))
                        Box(Modifier.weight(1f)) {
                            Column {
                                Text("Tanggal Lahir", fontFamily = PoppinsSemi, fontSize = 13.sp, color = BlueMain)
                                Spacer(Modifier.height(8.dp))
                                OutlinedTextField(
                                    value = tanggalLahir,
                                    onValueChange = {},
                                    readOnly = true,
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .clickable { showDatePicker = true },
                                    enabled = false,
                                    shape = RoundedCornerShape(12.dp),
                                    leadingIcon = { Icon(Icons.Default.CalendarToday, contentDescription = null, tint = BlueMain, modifier = Modifier.size(20.dp)) },
                                    textStyle = androidx.compose.ui.text.TextStyle(fontFamily = PoppinsReg, fontSize = 13.sp),
                                    colors = OutlinedTextFieldDefaults.colors(
                                        disabledTextColor = Color.Black,
                                        disabledBorderColor = Color.LightGray,
                                        disabledLeadingIconColor = BlueMain,
                                        disabledContainerColor = Color.Transparent
                                    )
                                )
                            }
                        }
                    }
                    Spacer(Modifier.height(16.dp))

                    // Pekerjaan Dropdown (Standardized for Naive Bayes)
                    var expandedPekerjaan by remember { mutableStateOf(false) }
                    val pekerjaanOptions = listOf("Karyawan Swasta", "PNS / ASN", "Wiraswasta", "Buruh", "Tidak Bekerja", "Lainnya")
                    
                    Column {
                        Text("Pekerjaan", fontFamily = PoppinsSemi, fontSize = 13.sp, color = BlueMain)
                        Spacer(Modifier.height(8.dp))
                        ExposedDropdownMenuBox(
                            expanded = expandedPekerjaan,
                            onExpandedChange = { expandedPekerjaan = !expandedPekerjaan }
                        ) {
                            OutlinedTextField(
                                value = pekerjaan,
                                onValueChange = {},
                                readOnly = true,
                                modifier = Modifier.fillMaxWidth().menuAnchor(),
                                shape = RoundedCornerShape(12.dp),
                                leadingIcon = { Icon(Icons.Default.Person, null, tint = BlueMain, modifier = Modifier.size(20.dp)) },
                                trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expandedPekerjaan) },
                                textStyle = androidx.compose.ui.text.TextStyle(fontFamily = PoppinsReg, fontSize = 14.sp),
                                colors = OutlinedTextFieldDefaults.colors(
                                    focusedBorderColor = BlueMain,
                                    unfocusedBorderColor = Color.LightGray
                                )
                            )
                            ExposedDropdownMenu(
                                expanded = expandedPekerjaan,
                                onDismissRequest = { expandedPekerjaan = false }
                            ) {
                                pekerjaanOptions.forEach { selectionOption ->
                                    DropdownMenuItem(
                                        text = { Text(selectionOption, fontFamily = PoppinsReg) },
                                        onClick = {
                                            pekerjaan = selectionOption
                                            expandedPekerjaan = false
                                        }
                                    )
                                }
                            }
                        }
                    }
                    Spacer(Modifier.height(16.dp))

                    Text("Jenis Kelamin", fontFamily = PoppinsSemi, fontSize = 13.sp, color = BlueMain)
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        RadioButton(selected = jenisKelamin == "Laki-laki", onClick = { jenisKelamin = "Laki-laki" })
                        Text("Laki-laki", fontSize = 14.sp, fontFamily = PoppinsReg)
                        Spacer(Modifier.width(16.dp))
                        RadioButton(selected = jenisKelamin == "Perempuan", onClick = { jenisKelamin = "Perempuan" })
                        Text("Perempuan", fontSize = 14.sp, fontFamily = PoppinsReg)
                    }

                    Spacer(Modifier.height(32.dp))

                    var localError by remember { mutableStateOf<String?>(null) }
                    
                    if (localError != null) {
                        Text(localError!!, color = Color.Red, fontSize = 12.sp, modifier = Modifier.padding(bottom = 8.dp))
                    }
                    if (state.error != null) {
                        Text(state.error!!, color = Color.Red, fontSize = 12.sp, modifier = Modifier.padding(bottom = 8.dp))
                    }

                    Button(
                        onClick = {
                            localError = when {
                                fullName.isBlank() -> "Nama lengkap harus diisi"
                                phone.isBlank() -> "Nomor telepon harus diisi"
                                blok.isBlank() -> "Blok rumah harus diisi"
                                noRumah.isBlank() -> "Nomor rumah harus diisi"
                                rt.isBlank() -> "RT harus diisi"
                                rw.isBlank() -> "RW harus diisi"
                                namaRt.isBlank() -> "Nama Ketua RT harus diisi"
                                houseType.isBlank() -> "Tipe rumah harus dipilih"
                                nik.length != 16 -> "NIK harus 16 digit"
                                tempatLahir.isBlank() -> "Tempat lahir harus diisi"
                                tanggalLahir.isBlank() -> "Tanggal lahir harus diisi"
                                pekerjaan.isBlank() -> "Pekerjaan harus diisi"
                                jenisKelamin.isBlank() -> "Pilih jenis kelamin"
                                else -> null
                            }
                            
                            if (localError == null) {
                                vm.updateProfile(fullName, phone, nik, blok, noRumah, rt, rw, namaRt, pekerjaan, tempatLahir, tanggalLahir, jenisKelamin, houseType)
                            }
                        },
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(56.dp),
                        shape = RoundedCornerShape(16.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = BlueMain),
                        enabled = !state.loading
                    ) {
                        if (state.loading) {
                            CircularProgressIndicator(color = Color.White, modifier = Modifier.size(24.dp))
                        } else {
                            Text("Simpan & Lanjutkan", fontFamily = PoppinsSemi, fontSize = 16.sp)
                        }
                    }
                    
                    Spacer(Modifier.height(40.dp))
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun ProfileTextField(
    label: String,
    value: String,
    icon: ImageVector,
    onValueChange: (String) -> Unit
) {
    Column {
        Text(label, fontFamily = PoppinsSemi, fontSize = 13.sp, color = BlueMain)
        Spacer(Modifier.height(8.dp))
        OutlinedTextField(
            value = value,
            onValueChange = onValueChange,
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(12.dp),
            leadingIcon = { Icon(icon, contentDescription = null, tint = BlueMain, modifier = Modifier.size(20.dp)) },
            textStyle = androidx.compose.ui.text.TextStyle(fontFamily = PoppinsReg, fontSize = 14.sp),
            colors = OutlinedTextFieldDefaults.colors(
                focusedBorderColor = BlueMain,
                unfocusedBorderColor = Color.LightGray,
                focusedContainerColor = Color.Transparent,
                unfocusedContainerColor = Color.Transparent
            )
        )
    }
}
