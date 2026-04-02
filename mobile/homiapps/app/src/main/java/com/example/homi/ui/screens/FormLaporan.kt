/**
 * File: app/src/main/java/com/example/homi/ui/screens/FormLaporan.kt
 */
@file:OptIn(ExperimentalMaterial3Api::class, ExperimentalFoundationApi::class)

package com.example.homi.ui.screens

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
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
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Close
import androidx.compose.material3.*
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.outlined.ReportProblem
import com.example.homi.ui.components.HomiDialog
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
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.repository.AccountRepository
import com.example.homi.data.repository.ComplaintRepository
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

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
fun FormLaporanScreen(
    complaintRepo: ComplaintRepository,
    accountRepo: AccountRepository,
    onBack: () -> Unit,
    onCreated: (Long) -> Unit
) {
    val ctx = LocalContext.current
    val scope = rememberCoroutineScope()

    var isSelf by rememberSaveable { mutableStateOf(true) }
    var profileData by remember { mutableStateOf<com.example.homi.data.model.FullProfileResponse?>(null) }

    var nama by rememberSaveable { mutableStateOf("") }
    var tempat by rememberSaveable { mutableStateOf("") }
    var perihal by rememberSaveable { mutableStateOf("") }
    var tanggalLahir by rememberSaveable { mutableStateOf("") } // Tanggal pengaduan

    // Date Picker
    val datePickerState = rememberDatePickerState()
    var showDatePicker by remember { mutableStateOf(false) }

    // Foto opsional
    var fotoUri by remember { mutableStateOf<Uri?>(null) }
    val pickImage = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        fotoUri = uri
    }

    var loading by remember { mutableStateOf(false) }
    var showSuccess by remember { mutableStateOf(false) }
    var errorMessage by remember { mutableStateOf<String?>(null) }

    // FETCH PROFILE
    LaunchedEffect(Unit) {
        scope.launch {
            runCatching { accountRepo.getFullProfile() }
                .onSuccess { profile ->
                    profileData = profile
                    if (isSelf) {
                        nama = profile.fullName ?: profile.name ?: ""
                        val rp = profile.residentProfile
                        val b = rp?.blok ?: ""
                        val n = rp?.noRumah ?: ""
                        if (b.isNotBlank() || n.isNotBlank()) {
                            tempat = "Blok $b No. $n".trim()
                        } else {
                            tempat = rp?.alamat ?: ""
                        }
                    }
                }
        }
    }

    // SYNC FORM WHEN TOGGLE SELF/OTHER
    LaunchedEffect(isSelf, profileData) {
        val p = profileData ?: return@LaunchedEffect
        if (isSelf) {
            nama = p.fullName ?: p.name ?: ""
            val rp = p.residentProfile
            val b = rp?.blok ?: ""
            val n = rp?.noRumah ?: ""
            if (b.isNotBlank() || n.isNotBlank()) {
                tempat = "Blok $b No. $n".trim()
            } else {
                tempat = rp?.alamat ?: ""
            }
        } else {
            nama = ""
            // Keep the 'tempat' if it was already filled, or clear it if preferred
            // Usually place of incident is different for "Other"
        }
    }

    // Date Picker Dialog
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

    fun submit() {
        val n = nama.trim()
        val t = tempat.trim()
        val p = perihal.trim()
        val dRaw = tanggalLahir.trim() // yyyy-MM-dd

        if (n.isBlank() || t.isBlank() || p.isBlank() || dRaw.isBlank()) {
            errorMessage = "Lengkapi semua field wajib."
            return
        }

        // Convert yyyy-MM-dd to ddMMyyyy for the backend (as expected by the current API logic)
        val d = try {
            val dateObj = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).parse(dRaw)
            SimpleDateFormat("ddMMyyyy", Locale.getDefault()).format(dateObj!!)
        } catch (e: Exception) {
            errorMessage = "Format tanggal tidak valid."
            return
        }

        loading = true
        errorMessage = null

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
                onCreated(id)
            } catch (e: Exception) {
                errorMessage = e.message ?: "Gagal mengirim laporan"
            } finally {
                loading = false
            }
        }
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
                    text = "Laporan Masalah",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(8.dp))

            Box(
                modifier = Modifier
                    .size(52.dp)
                    .background(Color.White.copy(alpha = 0.15f), RoundedCornerShape(16.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Outlined.ReportProblem,
                    contentDescription = null,
                    tint = Color.White,
                    modifier = Modifier.size(26.dp)
                )
            }

            Spacer(Modifier.height(8.dp))
            Text(
                text = "Laporkan kendala di sekitar Anda untuk\npenanganan lebih lanjut dari pengurus.",
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
                    text = "Pilih Pelapor",
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
                    text = "Data Laporan",
                    fontFamily = PoppinsSemi,
                    fontSize = 15.sp,
                    color = BlueMain
                )

                Spacer(Modifier.height(20.dp))

                HomiFormField(
                    label = "Nama Pelapor",
                    placeholder = "Misal: Budi Santoso",
                    value = nama,
                    onValueChange = { nama = it },
                    enabled = !isSelf
                )

                Spacer(Modifier.height(16.dp))

                Column {
                    Text("Tanggal Kejadian", fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextDark)
                    Spacer(Modifier.height(6.dp))
                    OutlinedTextField(
                        value = tanggalLahir,
                        onValueChange = {},
                        readOnly = true,
                        modifier = Modifier
                            .fillMaxWidth()
                            .clickable { showDatePicker = true },
                        enabled = false,
                        shape = RoundedCornerShape(14.dp),
                        textStyle = androidx.compose.ui.text.TextStyle(fontFamily = PoppinsReg, fontSize = 14.sp, color = TextDark),
                        placeholder = { Text("Pilih Tanggal", fontSize = 14.sp, color = HintGray) },
                        colors = OutlinedTextFieldDefaults.colors(
                            disabledTextColor = TextDark,
                            disabledBorderColor = FieldBorder,
                            disabledContainerColor = FieldBg
                        ),
                        trailingIcon = {
                            Icon(
                                painter = painterResource(R.drawable.ic_calendar),
                                contentDescription = null,
                                tint = BlueMain,
                                modifier = Modifier.size(20.dp).clickable { showDatePicker = true }
                            )
                        }
                    )
                }

                Spacer(Modifier.height(16.dp))

                HomiFormField(
                    label = "Tempat Kejadian",
                    placeholder = "Contoh: Blok A / Pos Satpam",
                    value = tempat,
                    onValueChange = { tempat = it }
                )

                Spacer(Modifier.height(16.dp))

                HomiFormField(
                    label = "Perihal Pengaduan",
                    placeholder = "Contoh: Sampah berserakan / Lampu mati",
                    value = perihal,
                    onValueChange = { perihal = it },
                    singleLine = false,
                    minLines = 3
                )

                Spacer(Modifier.height(16.dp))

                // Upload foto opsional
                Text(
                    text = "Foto Pendukung (Opsional)",
                    fontFamily = PoppinsSemi,
                    fontSize = 13.sp,
                    color = TextDark
                )
                Spacer(Modifier.height(8.dp))
                
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(14.dp),
                    border = BorderStroke(1.dp, FieldBorder),
                    colors = CardDefaults.cardColors(containerColor = FieldBg)
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        OutlinedButton(
                            onClick = { pickImage.launch("image/*") },
                            shape = RoundedCornerShape(12.dp),
                            border = BorderStroke(1.dp, BlueMain),
                            modifier = Modifier.height(42.dp),
                            enabled = !loading
                        ) {
                            Text(
                                "Upload Foto",
                                fontFamily = PoppinsSemi,
                                fontSize = 12.sp,
                                color = BlueMain
                            )
                        }

                        Spacer(Modifier.width(12.dp))

                        if (fotoUri != null) {
                            Text(
                                text = "Lampiran dipilih",
                                fontFamily = PoppinsReg,
                                fontSize = 12.sp,
                                color = SuccessGreen,
                                modifier = Modifier.weight(1f)
                            )
                            IconButton(onClick = { fotoUri = null }) {
                                Icon(
                                    imageVector = Icons.Default.Close,
                                    contentDescription = "Hapus",
                                    tint = ErrorRed,
                                    modifier = Modifier.size(20.dp)
                                )
                            }
                        } else {
                            Text(
                                text = "Belum ada foto",
                                fontFamily = PoppinsReg,
                                fontSize = 12.sp,
                                color = HintGray
                            )
                        }
                    }
                }

                if (errorMessage != null) {
                    Spacer(Modifier.height(16.dp))
                    Text(
                        text = errorMessage!!,
                        fontFamily = PoppinsReg,
                        fontSize = 12.sp,
                        color = ErrorRed,
                        modifier = Modifier.fillMaxWidth(),
                        textAlign = TextAlign.Center
                    )
                }

                Spacer(Modifier.height(32.dp))

                Button(
                    onClick = { submit() },
                    enabled = !loading,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(52.dp),
                    shape = RoundedCornerShape(16.dp),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = AccentOrange,
                        disabledContainerColor = AccentOrange.copy(alpha = 0.45f)
                    ),
                    elevation = ButtonDefaults.buttonElevation(defaultElevation = 4.dp)
                ) {
                    if (loading) {
                        CircularProgressIndicator(modifier = Modifier.size(20.dp), color = Color.White, strokeWidth = 2.dp)
                    } else {
                        Text(
                            text = "Kirim Pengaduan",
                            fontFamily = PoppinsSemi,
                            color = Color.White,
                            fontSize = 15.sp
                        )
                    }
                }

                Spacer(Modifier.height(24.dp))
            }
        }
    }

    if (showSuccess) {
        HomiDialog(
            onDismissRequest = { showSuccess = false },
            title = "Berhasil Terkirim!",
            description = "Laporan Anda telah berhasil diajukan dan akan segera diproses oleh pengurus.",
            icon = Icons.Default.CheckCircle,
            iconTint = SuccessGreen,
            confirmButtonText = "Selesai",
            onConfirm = { showSuccess = false }
        )
    }
}

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
