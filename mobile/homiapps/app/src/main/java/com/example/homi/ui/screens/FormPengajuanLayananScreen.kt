@file:OptIn(ExperimentalMaterial3Api::class, ExperimentalFoundationApi::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.ExperimentalFoundationApi
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.relocation.BringIntoViewRequester
import androidx.compose.foundation.relocation.bringIntoViewRequester
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Handyman
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.focus.onFocusEvent
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.RequestTypeDto
import com.example.homi.data.repository.AccountRepository
import com.example.homi.data.repository.ServiceRequestRepository
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

private data class LayananOption(
    val id: String,
    val title: String,
    val desc: String
)

@Composable
fun FormPengajuanLayananScreen(
    repo: ServiceRequestRepository,
    accountRepo: AccountRepository,
    submitting: Boolean,
    onBack: () -> Unit,
    onSubmit: (requestTypeId: Int, subject: String, payload: Map<String, String>) -> Unit
) {
    val layananOptions = remember {
        listOf(
            LayananOption("fasilitas", "Perbaikan Fasilitas", "Lampu jalan, jalan rusak, drainase, taman."),
            LayananOption("kebersihan", "Kebersihan Lingkungan", "Sampah menumpuk, saluran mampet, area kotor."),
            LayananOption("keamanan", "Keamanan Komplek", "Laporan keamanan, patroli, tamu mencurigakan."),
            LayananOption("izin", "Perizinan Kegiatan", "Izin acara warga dan pemakaian fasilitas umum."),
            LayananOption("lainnya", "Layanan Lainnya", "Permintaan layanan lain di luar kategori di atas.")
        )
    }

    var requestTypes by remember { mutableStateOf<List<RequestTypeDto>>(emptyList()) }
    var loadingTypes by remember { mutableStateOf(true) }
    var typeError by remember { mutableStateOf<String?>(null) }

    var isSelf by remember { mutableStateOf(true) }
    var profileData by remember { mutableStateOf<com.example.homi.data.model.FullProfileResponse?>(null) }

    var selected by remember { mutableStateOf(layananOptions.first().id) }
    var perihal by remember { mutableStateOf("") }
    var nama by remember { mutableStateOf("") }
    var lokasi by remember { mutableStateOf("") }
    var rt by remember { mutableStateOf("") }
    var rw by remember { mutableStateOf("") }
    var namaRt by remember { mutableStateOf("") }
    var detail by remember { mutableStateOf("") }
    var noHp by remember { mutableStateOf("") }
    var nik by remember { mutableStateOf("") }
    var uiError by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        loadingTypes = true
        typeError = null
        
        // Parallel pre-fill profile & request types
        launch {
            runCatching { repo.getRequestTypes() }
                .onSuccess { requestTypes = it }
                .onFailure { typeError = it.message ?: "Gagal memuat tipe layanan." }
            loadingTypes = false
        }
        
        launch {
            runCatching { accountRepo.getFullProfile() }
                .onSuccess { profile ->
                    profileData = profile
                    // Selalu perbarui jika isSelf
                    if (isSelf) {
                        nama = profile.fullName ?: profile.name ?: ""
                        noHp = profile.phone ?: ""
                        rt = profile.residentProfile?.rt ?: ""
                        rw = profile.residentProfile?.rw ?: ""
                        namaRt = profile.residentProfile?.namaRt ?: ""
                        
                        val blokStr = profile.residentProfile?.blok ?: ""
                        val noStr = profile.residentProfile?.noRumah ?: ""
                        lokasi = "Perumahan Hawai Garden Blok $blokStr No. $noStr".trim()
                    }
                }
        }
    }

    LaunchedEffect(isSelf, profileData) {
        val p = profileData ?: return@LaunchedEffect
        if (isSelf) {
            val raw = p.fullName ?: p.name ?: ""
            nama = if (raw.lowercase().contains("warga")) "" else raw
            
            noHp = p.phone ?: ""
            nik = p.residentProfile?.nik ?: ""
            rt = p.residentProfile?.rt ?: ""
            rw = p.residentProfile?.rw ?: ""
            namaRt = p.residentProfile?.namaRt ?: ""
            
            val blokStr = p.residentProfile?.blok ?: ""
            val noStr = p.residentProfile?.noRumah ?: ""
            lokasi = "Perumahan Hawai Garden Blok $blokStr No. $noStr".trim()
        } else {
            // Jika pindah ke orang lain, kita kosongkan nama, noHp, & nik
            nama = ""
            noHp = ""
            nik = ""
        }
    }

    val selectedLabel = remember(selected) {
        layananOptions.firstOrNull { it.id == selected }?.title ?: "Layanan Warga"
    }

    val canSubmit = !submitting && !loadingTypes && nama.isNotBlank() && nik.isNotBlank() && 
                    lokasi.isNotBlank() && detail.isNotBlank() && perihal.isNotBlank() &&
                    rt.isNotBlank() && rw.isNotBlank() && namaRt.isNotBlank()

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
                IconButton(onClick = onBack, modifier = Modifier.align(Alignment.CenterStart)) {
                    Icon(
                        painter = painterResource(id = R.drawable.panahkembali),
                        contentDescription = "Kembali",
                        tint = Color.White,
                        modifier = Modifier.size(24.dp)
                    )
                }

                Text(
                    text = "Pengajuan Layanan",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(12.dp))
            Text(
                text = "Laporkan kebutuhan perbaikan atau\nbantuan layanan lingkungan Anda.",
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
                Text("Kategori Layanan", fontFamily = PoppinsSemi, fontSize = 15.sp, color = BlueMain)
                Spacer(Modifier.height(12.dp))
                
                // Horizontal category chips
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    layananOptions.forEach { item ->
                        val active = selected == item.id
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .background(if (active) BlueMain.copy(alpha = 0.05f) else Color.Transparent, RoundedCornerShape(12.dp))
                                .border(1.dp, if (active) BlueMain else FieldBorder, RoundedCornerShape(12.dp))
                                .clickable { selected = item.id }
                                .padding(horizontal = 16.dp, vertical = 12.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            RadioButton(
                                selected = active,
                                onClick = { selected = item.id },
                                colors = RadioButtonDefaults.colors(selectedColor = BlueMain)
                            )
                            Spacer(Modifier.width(8.dp))
                            Column {
                                Text(item.title, fontFamily = PoppinsSemi, fontSize = 14.sp, color = if (active) BlueMain else TextDark)
                                Text(item.desc, fontFamily = PoppinsReg, fontSize = 11.sp, color = HintGray)
                            }
                        }
                    }
                }

                Spacer(Modifier.height(24.dp))
                HorizontalDivider(color = Color(0xFFF1F5F9), thickness = 1.dp)
                Spacer(Modifier.height(20.dp))

                Text("Pilih Pengaju", fontFamily = PoppinsSemi, fontSize = 15.sp, color = BlueMain)
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
                Text("Informasi Pelapor", fontFamily = PoppinsSemi, fontSize = 15.sp, color = BlueMain)
                Spacer(Modifier.height(20.dp))

                HomiFormField("Nama Pemohon", "Nama Lengkap Anda", nama, { nama = it }, enabled = !isSelf || nama.isBlank())
                Spacer(Modifier.height(16.dp))
                HomiFormField("NIK", "Masukkan 16 digit NIK", nik, { nik = it.filter { c -> c.isDigit() }.take(16) }, keyboardType = KeyboardType.Number, enabled = !isSelf)
                Spacer(Modifier.height(16.dp))
                HomiFormField("Lokasi / Alamat", "Misal: Blok B No. 12", lokasi, { lokasi = it }, enabled = false)
                Spacer(Modifier.height(16.dp))
                
                Row(modifier = Modifier.fillMaxWidth()) {
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField("RT", "001", rt, { rt = it }, keyboardType = KeyboardType.Number)
                    }
                    Spacer(Modifier.width(12.dp))
                    Box(modifier = Modifier.weight(1f)) {
                        HomiFormField("RW", "002", rw, { rw = it }, keyboardType = KeyboardType.Number)
                    }
                }
                Spacer(Modifier.height(16.dp))
                HomiFormField("Nama Ketua RT", "Nama Ketua RT Anda", namaRt, { namaRt = it })
                Spacer(Modifier.height(16.dp))

                HomiFormField("No. HP / WhatsApp", "08xxxxxxxxxx", noHp, { noHp = it }, keyboardType = KeyboardType.Phone, enabled = !isSelf)

                Spacer(Modifier.height(24.dp))
                HorizontalDivider(color = Color(0xFFF1F5F9), thickness = 1.dp)
                Spacer(Modifier.height(20.dp))

                Text("Detail Permohonan", fontFamily = PoppinsSemi, fontSize = 15.sp, color = BlueMain)
                Spacer(Modifier.height(20.dp))

                HomiFormField("Perihal", "Contoh: Perbaikan Lampu Jalan", perihal, { perihal = it })
                Spacer(Modifier.height(16.dp))

                HomiFormField(
                    label = "Jelaskan Kebutuhan / Keluhan",
                    placeholder = "Tuliskan secara detail agar kami dapat segera menindaklanjuti...",
                    value = detail,
                    onValueChange = { detail = it },
                    singleLine = false,
                    minLines = 4
                )

                Spacer(Modifier.height(32.dp))

                if (loadingTypes) {
                    LinearProgressIndicator(modifier = Modifier.fillMaxWidth().height(2.dp), color = BlueMain)
                    Spacer(Modifier.height(16.dp))
                }

                val errorText = uiError ?: typeError
                if (!errorText.isNullOrBlank()) {
                    Text(errorText, fontFamily = PoppinsReg, fontSize = 12.sp, color = ErrorRed, modifier = Modifier.fillMaxWidth(), textAlign = TextAlign.Center)
                    Spacer(Modifier.height(12.dp))
                }

                Button(
                    onClick = {
                        uiError = null
                        val selectedTypeId = pickRequestTypeIdForLayanan(requestTypes, selectedLabel)
                        if (selectedTypeId == null) {
                            uiError = "Tipe layanan belum tersedia di sistem."
                            return@Button
                        }
                        val subject = if (perihal.isNotBlank()) perihal else "Permohonan $selectedLabel - $nama"
                        val payload = mapOf(
                            "pengaju" to (if (isSelf) "Diri Sendiri" else "Orang Lain"),
                            "nama_warga" to nama.trim(),
                            "nik" to nik.trim(),
                            "blok" to lokasi.trim(),
                            "rt" to rt.trim(),
                            "rw" to rw.trim(),
                            "nama_rt" to namaRt.trim(),
                            "noRumah" to "-",
                            "kategori_layanan" to selectedLabel,
                            "perihal" to perihal.trim(),
                            "detail_layanan" to detail.trim(),
                            "pj" to "KETUA RT"
                        )
                        onSubmit(selectedTypeId, subject, payload)
                    },
                    enabled = canSubmit,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = BlueMain,
                        disabledContainerColor = BlueMain.copy(alpha = 0.45f)
                    ),
                    shape = RoundedCornerShape(16.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(54.dp),
                    elevation = ButtonDefaults.buttonElevation(defaultElevation = 4.dp)
                ) {
                    if (submitting) {
                        CircularProgressIndicator(modifier = Modifier.size(22.dp), strokeWidth = 2.dp, color = Color.White)
                        Spacer(Modifier.width(12.dp))
                    }
                    Text("Kirim Permohonan", fontFamily = PoppinsSemi, color = Color.White, fontSize = 15.sp)
                }

                Spacer(Modifier.height(12.dp))
                Text(
                    text = if (canSubmit) "✓ Data permohonan siap dikirim." else "✕ Mohon lengkapi semua field bertanda.",
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = if (canSubmit) SuccessGreen else ErrorRed,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center
                )
                
                Spacer(Modifier.height(32.dp))
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

private fun pickRequestTypeIdForLayanan(types: List<RequestTypeDto>, selectedLabel: String): Int? {
    if (types.isEmpty()) return null
    val lowerLabel = selectedLabel.lowercase()
    val preferred = types.firstOrNull { t ->
        val n = t.name.lowercase()
        n.contains("layanan") || n.contains("umum") || n.contains(lowerLabel)
    }
    return preferred?.id ?: types.firstOrNull()?.id
}
