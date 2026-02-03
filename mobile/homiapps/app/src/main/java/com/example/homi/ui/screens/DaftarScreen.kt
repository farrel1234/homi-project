package com.example.homi.ui.screens

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.focus.FocusDirection
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.local.TokenStore
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.AuthRepository
import kotlinx.coroutines.launch
import retrofit2.HttpException

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DaftarScreen(
    tokenStore: TokenStore,
    onGoLogin: () -> Unit,
    onGoOtp: (
        email: String,
        job: String,
        houseType: String,
        housing: String,
        block: String,
        houseNumber: String
    ) -> Unit,
) {
    // Theme HOMI
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val blue = Color(0xFF256D85)
    val orange = Color(0xFFFFA06B)
    val bg = Color(0xFFF6FAFC)

    // Form state
    var fullName by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }

    // NB profile
    var job by remember { mutableStateOf("") }
    var housing by remember { mutableStateOf("Hawai Garden") }
    var block by remember { mutableStateOf("") }
    var houseNumber by remember { mutableStateOf("") }

    // Rumah dropdown
    val houseTypes = remember { listOf("Tipe 36", "Tipe 45", "Tipe 54", "Tipe 60", "Lainnya") }
    var houseType by remember { mutableStateOf("") }
    var houseTypeExpanded by remember { mutableStateOf(false) }

    // Password
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }

    var loading by remember { mutableStateOf(false) }

    val snackbar = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()
    val focus = LocalFocusManager.current
    val scroll = rememberScrollState()

    val api = remember { ApiClient.getApi(tokenStore) }
    val authRepo = remember { AuthRepository(api) }

    // Validasi
    val isEmailValid = remember(email) { email.trim().contains("@") && email.trim().contains(".") }
    val isPassValid = remember(password) { password.length >= 6 }
    val isFormValid =
        fullName.isNotBlank() &&
                isEmailValid &&
                isPassValid &&
                password == confirmPassword &&
                job.isNotBlank() &&
                housing.isNotBlank() &&
                houseType.isNotBlank() &&
                block.isNotBlank() &&
                houseNumber.isNotBlank()

    val tfColors = OutlinedTextFieldDefaults.colors(
        focusedContainerColor = Color(0xFFF5F5F5),
        unfocusedContainerColor = Color(0xFFF5F5F5),
        focusedBorderColor = blue,
        unfocusedBorderColor = Color.Gray,
        cursorColor = blue
    )

    fun extractApiMessage(raw: String?): String? {
        if (raw.isNullOrBlank()) return null
        // cari "message":"...."
        val regex = Regex("\"message\"\\s*:\\s*\"(.*?)\"")
        val match = regex.find(raw) ?: return null
        return match.groupValues.getOrNull(1)
            ?.replace("\\n", "\n")
            ?.replace("\\\"", "\"")
    }

    suspend fun showError(e: Throwable) {
        val msg = when (e) {
            is HttpException -> {
                val body = runCatching { e.response()?.errorBody()?.string() }.getOrNull()
                extractApiMessage(body) ?: e.message()
            }
            else -> e.message ?: "Terjadi kesalahan."
        }
        snackbar.showSnackbar(msg)
    }

    Scaffold(
        snackbarHost = { SnackbarHost(snackbar) },
        containerColor = bg
    ) { pad ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(pad)
                .imePadding()
                .navigationBarsPadding()
                .verticalScroll(scroll)
                .padding(horizontal = 20.dp, vertical = 16.dp)
        ) {
            Spacer(Modifier.height(4.dp))

            Text(
                text = "Daftar",
                fontSize = 24.sp,
                fontFamily = poppins,
                fontWeight = FontWeight.Bold,
                color = blue
            )
            Text(
                text = "Buat akun HOMI untuk mengakses layanan warga.",
                fontSize = 12.sp,
                fontFamily = poppins,
                color = Color(0xFF4B5563)
            )

            Spacer(Modifier.height(16.dp))

            Card(
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(modifier = Modifier.padding(16.dp)) {

                    // Nama
                    OutlinedTextField(
                        value = fullName,
                        onValueChange = { fullName = it },
                        label = { Text("Nama Lengkap", fontFamily = poppins) },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = tfColors,
                        enabled = !loading,
                        keyboardOptions = KeyboardOptions(
                            keyboardType = KeyboardType.Text,
                            imeAction = ImeAction.Next
                        ),
                        keyboardActions = KeyboardActions(
                            onNext = { focus.moveFocus(FocusDirection.Down) }
                        )
                    )

                    Spacer(Modifier.height(12.dp))

                    // Email
                    OutlinedTextField(
                        value = email,
                        onValueChange = { email = it },
                        label = { Text("Email", fontFamily = poppins) },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = tfColors,
                        enabled = !loading,
                        keyboardOptions = KeyboardOptions(
                            keyboardType = KeyboardType.Email,
                            imeAction = ImeAction.Next
                        ),
                        keyboardActions = KeyboardActions(
                            onNext = { focus.moveFocus(FocusDirection.Down) }
                        ),
                        supportingText = {
                            if (email.isNotBlank() && !isEmailValid) {
                                Text("Format email tidak valid", color = Color.Red, fontSize = 11.sp)
                            }
                        }
                    )

                    Spacer(Modifier.height(12.dp))

                    // Tipe Rumah (dropdown)
                    ExposedDropdownMenuBox(
                        expanded = houseTypeExpanded,
                        onExpandedChange = { if (!loading) houseTypeExpanded = !houseTypeExpanded },
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        OutlinedTextField(
                            value = houseType,
                            onValueChange = {},
                            readOnly = true,
                            label = { Text("Tipe Rumah", fontFamily = poppins) },
                            trailingIcon = {
                                ExposedDropdownMenuDefaults.TrailingIcon(expanded = houseTypeExpanded)
                            },
                            modifier = Modifier
                                .fillMaxWidth()
                                .menuAnchor(),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading
                        )
                        ExposedDropdownMenu(
                            expanded = houseTypeExpanded,
                            onDismissRequest = { houseTypeExpanded = false }
                        ) {
                            houseTypes.forEach { item ->
                                DropdownMenuItem(
                                    text = { Text(item, fontFamily = poppins) },
                                    onClick = {
                                        houseType = item
                                        houseTypeExpanded = false
                                    }
                                )
                            }
                        }
                    }

                    Spacer(Modifier.height(12.dp))

                    // Pekerjaan
                    OutlinedTextField(
                        value = job,
                        onValueChange = { job = it },
                        label = { Text("Pekerjaan", fontFamily = poppins) },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = tfColors,
                        enabled = !loading,
                        keyboardOptions = KeyboardOptions(
                            keyboardType = KeyboardType.Text,
                            imeAction = ImeAction.Next
                        ),
                        keyboardActions = KeyboardActions(
                            onNext = { focus.moveFocus(FocusDirection.Down) }
                        )
                    )

                    Spacer(Modifier.height(12.dp))

                    // Perumahan
                    OutlinedTextField(
                        value = housing,
                        onValueChange = { housing = it },
                        label = { Text("Perumahan", fontFamily = poppins) },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = tfColors,
                        enabled = !loading,
                        keyboardOptions = KeyboardOptions(
                            keyboardType = KeyboardType.Text,
                            imeAction = ImeAction.Next
                        ),
                        keyboardActions = KeyboardActions(
                            onNext = { focus.moveFocus(FocusDirection.Down) }
                        )
                    )

                    Spacer(Modifier.height(12.dp))

                    // Blok & Nomor Rumah (2 kolom)
                    Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                        OutlinedTextField(
                            value = block,
                            onValueChange = { block = it },
                            label = { Text("Blok", fontFamily = poppins) },
                            singleLine = true,
                            modifier = Modifier.weight(1f),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            keyboardOptions = KeyboardOptions(
                                keyboardType = KeyboardType.Text,
                                imeAction = ImeAction.Next
                            ),
                            keyboardActions = KeyboardActions(
                                onNext = { focus.moveFocus(FocusDirection.Down) }
                            )
                        )
                        OutlinedTextField(
                            value = houseNumber,
                            onValueChange = { houseNumber = it },
                            label = { Text("Nomor", fontFamily = poppins) },
                            singleLine = true,
                            modifier = Modifier.weight(1f),
                            shape = RoundedCornerShape(12.dp),
                            colors = tfColors,
                            enabled = !loading,
                            keyboardOptions = KeyboardOptions(
                                keyboardType = KeyboardType.Number,
                                imeAction = ImeAction.Next
                            ),
                            keyboardActions = KeyboardActions(
                                onNext = { focus.moveFocus(FocusDirection.Down) }
                            )
                        )
                    }

                    Spacer(Modifier.height(12.dp))

                    // Password
                    OutlinedTextField(
                        value = password,
                        onValueChange = { password = it },
                        label = { Text("Kata Sandi", fontFamily = poppins) },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = tfColors,
                        enabled = !loading,
                        visualTransformation = PasswordVisualTransformation(),
                        keyboardOptions = KeyboardOptions(
                            keyboardType = KeyboardType.Password,
                            imeAction = ImeAction.Next
                        ),
                        keyboardActions = KeyboardActions(
                            onNext = { focus.moveFocus(FocusDirection.Down) }
                        ),
                        supportingText = {
                            if (password.isNotBlank() && !isPassValid) {
                                Text("Password minimal 6 karakter", color = Color.Red, fontSize = 11.sp)
                            }
                        }
                    )

                    Spacer(Modifier.height(12.dp))

                    // Konfirmasi Password
                    OutlinedTextField(
                        value = confirmPassword,
                        onValueChange = { confirmPassword = it },
                        label = { Text("Konfirmasi Kata Sandi", fontFamily = poppins) },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = tfColors,
                        enabled = !loading,
                        visualTransformation = PasswordVisualTransformation(),
                        keyboardOptions = KeyboardOptions(
                            keyboardType = KeyboardType.Password,
                            imeAction = ImeAction.Done
                        ),
                        keyboardActions = KeyboardActions(
                            onDone = { focus.clearFocus() }
                        ),
                        supportingText = {
                            if (confirmPassword.isNotBlank() && confirmPassword != password) {
                                Text("Konfirmasi tidak sama", color = Color.Red, fontSize = 11.sp)
                            }
                        }
                    )

                    Spacer(Modifier.height(18.dp))

                    Button(
                        onClick = {
                            focus.clearFocus()
                            if (!isFormValid || loading) return@Button

                            loading = true
                            scope.launch {
                                try {
                                    authRepo.register(
                                        name = fullName.trim(),
                                        email = email.trim(),
                                        password = password
                                    )
                                    loading = false
                                    snackbar.showSnackbar("OTP dikirim ke email. Silakan verifikasi.")
                                    onGoOtp(
                                        email.trim(),
                                        job.trim(),
                                        houseType.trim(),
                                        housing.trim(),
                                        block.trim(),
                                        houseNumber.trim()
                                    )
                                } catch (e: Throwable) {
                                    loading = false
                                    showError(e)
                                }
                            }
                        },
                        enabled = isFormValid && !loading,
                        colors = ButtonDefaults.buttonColors(containerColor = orange),
                        shape = RoundedCornerShape(14.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(52.dp)
                    ) {
                        if (loading) {
                            CircularProgressIndicator(
                                modifier = Modifier.size(18.dp),
                                strokeWidth = 2.dp,
                                color = Color.White
                            )
                            Spacer(Modifier.width(10.dp))
                        }
                        Text(
                            text = if (loading) "Memproses..." else "Konfirmasi",
                            color = Color.White,
                            fontFamily = poppins,
                            fontWeight = FontWeight.SemiBold
                        )
                    }

                    Spacer(Modifier.height(14.dp))

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.Center
                    ) {
                        Text(
                            "Sudah punya akun?",
                            fontSize = 11.sp,
                            fontFamily = poppins,
                            color = Color.Black
                        )
                        Spacer(Modifier.width(6.dp))
                        Text(
                            text = "Masuk",
                            fontSize = 11.sp,
                            fontFamily = poppins,
                            fontWeight = FontWeight.Bold,
                            color = blue,
                            textDecoration = TextDecoration.Underline,
                            modifier = Modifier.clickable(enabled = !loading) { onGoLogin() }
                        )
                    }
                }
            }

            Spacer(Modifier.height(18.dp))
        }
    }
}

@Preview(showSystemUi = true, showBackground = true)
@Composable
private fun DaftarScreenPreview() {
    MaterialTheme { /* preview only */ }
}
