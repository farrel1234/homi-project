package com.example.homi.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.focus.FocusRequester
import androidx.compose.ui.focus.focusRequester
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavController
import com.example.homi.R
import com.example.homi.data.local.TokenStore
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.AuthRepository
import com.example.homi.navigation.Routes
import kotlinx.coroutines.launch

@Composable
fun KonfirmasiDaftarScreen(
    navController: NavController,
    tokenStore: TokenStore
) {
    // Theme HOMI
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val blue = Color(0xFF256D85)
    val orange = Color(0xFFFFA06B)
    val bg = Color(0xFFF6FAFC)
    val cardBg = Color.White

    val snackbar = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()
    val focus = LocalFocusManager.current

    // data dari savedStateHandle (yang kamu set dari DaftarScreen via NavHost)
    val prev = navController.previousBackStackEntry?.savedStateHandle
    val email = prev?.get<String>("register_email").orEmpty()
    val job = prev?.get<String>("register_job").orEmpty()
    val houseType = prev?.get<String>("register_house_type").orEmpty()
    val housing = prev?.get<String>("register_housing").orEmpty()

    // ✅ ambil block & nomor rumah juga
    val block = prev?.get<String>("register_block").orEmpty()
    val houseNumber = prev?.get<String>("register_house_number").orEmpty()

    var loading by remember { mutableStateOf(false) }

    // OTP 6 digit
    val digits = remember { mutableStateListOf("", "", "", "", "", "") }
    val reqs = remember { List(6) { FocusRequester() } }

    val api = remember { ApiClient.getApi(tokenStore) }
    val repo = remember { AuthRepository(api) }

    fun otpValue(): String = digits.joinToString("")

    Scaffold(
        snackbarHost = { SnackbarHost(snackbar) },
        containerColor = bg
    ) { pad ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(pad)
                .navigationBarsPadding()
                .imePadding()
                .padding(horizontal = 20.dp, vertical = 16.dp)
        ) {
            Spacer(Modifier.height(6.dp))

            Text(
                text = "Verifikasi OTP",
                fontFamily = poppins,
                fontWeight = FontWeight.Bold,
                fontSize = 24.sp,
                color = blue
            )
            Text(
                text = if (email.isNotBlank()) "Kode dikirim ke: $email" else "Email tidak ditemukan.",
                fontFamily = poppins,
                fontSize = 12.sp,
                color = Color(0xFF4B5563)
            )

            Spacer(Modifier.height(16.dp))

            Card(
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = cardBg),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
            ) {
                Column(
                    modifier = Modifier.padding(16.dp)
                ) {
                    Text(
                        text = "Masukkan 6 digit kode OTP",
                        fontFamily = poppins,
                        fontSize = 13.sp,
                        color = Color.Black
                    )

                    Spacer(Modifier.height(12.dp))

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        for (i in 0 until 6) {
                            OutlinedTextField(
                                value = digits[i],
                                onValueChange = { v ->
                                    val only = v.filter { it.isDigit() }.take(1)
                                    digits[i] = only
                                    if (only.isNotEmpty() && i < 5) reqs[i + 1].requestFocus()
                                },
                                singleLine = true,
                                textStyle = TextStyle(
                                    fontFamily = poppins,
                                    fontSize = 18.sp,
                                    fontWeight = FontWeight.Bold,
                                    textAlign = TextAlign.Center
                                ),
                                modifier = Modifier
                                    .width(48.dp)
                                    .height(56.dp)
                                    .focusRequester(reqs[i]),
                                shape = RoundedCornerShape(12.dp),
                                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                                colors = OutlinedTextFieldDefaults.colors(
                                    focusedContainerColor = Color(0xFFF5F5F5),
                                    unfocusedContainerColor = Color(0xFFF5F5F5),
                                    focusedBorderColor = blue,
                                    unfocusedBorderColor = Color.Gray,
                                    cursorColor = blue
                                )
                            )
                        }
                    }

                    Spacer(Modifier.height(16.dp))

                    Button(
                        onClick = {
                            focus.clearFocus()
                            val otp = otpValue()

                            if (email.isBlank()) {
                                scope.launch { snackbar.showSnackbar("Email kosong. Kembali ke halaman daftar.") }
                                return@Button
                            }
                            if (otp.length != 6) {
                                scope.launch { snackbar.showSnackbar("OTP harus 6 digit.") }
                                return@Button
                            }
                            if (loading) return@Button

                            loading = true
                            scope.launch {
                                try {
                                    val verified = repo.verifyOtp(email, otp)

                                    tokenStore.saveToken(verified.token)
                                    tokenStore.saveName(verified.user.name)

                                    // ✅ simpan profil NB (tetap 3 field dulu)
                                    if (job.isNotBlank() && houseType.isNotBlank() && housing.isNotBlank()) {
                                        repo.saveNaiveBayesProfile(
                                            houseType = houseType,
                                            job = job,
                                            housing = housing,
                                            block = block,
                                            houseNumber = houseNumber,
                                            upsertCall = api::upsertResidentProfileMap
                                        )

                                    }

                                    // NOTE:
                                    // block & houseNumber SUDAH kamu punya di sini:
                                    // block = "$block", houseNumber = "$houseNumber"
                                    // Kalau mau otomatis masuk direktori di backend,
                                    // nanti kita tambahin endpoint + repo method khusus untuk simpan blok/no_rumah.

                                    loading = false
                                    snackbar.showSnackbar("Verifikasi berhasil!")

                                    navController.navigate(Routes.Beranda) {
                                        popUpTo(Routes.Login) { inclusive = true }
                                        launchSingleTop = true
                                    }
                                } catch (e: Exception) {
                                    loading = false
                                    snackbar.showSnackbar(e.message ?: "Verifikasi gagal.")
                                }
                            }
                        },
                        enabled = !loading,
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
                            text = "Verifikasi",
                            fontFamily = poppins,
                            fontWeight = FontWeight.SemiBold,
                            color = Color.White
                        )
                    }

                    Spacer(Modifier.height(10.dp))

                    TextButton(
                        onClick = { navController.popBackStack() },
                        modifier = Modifier.align(Alignment.End)
                    ) {
                        Text("Kembali", fontFamily = poppins, color = blue)
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // kecil aja buat debug (boleh hapus)
            if (block.isNotBlank() || houseNumber.isNotBlank()) {
                Text(
                    text = "Alamat: Blok $block No $houseNumber",
                    fontFamily = poppins,
                    fontSize = 11.sp,
                    color = Color(0xFF6B7280),
                    modifier = Modifier.padding(start = 2.dp)
                )
            }
        }
    }

    // fokus ke kotak pertama
    LaunchedEffect(Unit) { reqs[0].requestFocus() }
}
