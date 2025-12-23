package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.ui.viewmodel.AuthViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LoginScreen(
    vm: AuthViewModel,
    onLoginSuccess: () -> Unit,
    onRegisterClicked: () -> Unit = {},
    onForgotPasswordClicked: () -> Unit = {},
) {
    val poppins = FontFamily(Font(R.font.poppins_semibold))

    val state by vm.state.collectAsState()

    var identifier by remember { mutableStateOf("") } // input user
    var password by remember { mutableStateOf("") }
    var passwordVisible by remember { mutableStateOf(false) }
    var errorText by remember { mutableStateOf<String?>(null) }

    // kalau login sukses dari API -> navigate
    LaunchedEffect(state.isLoggedIn) {
        if (state.isLoggedIn) onLoginSuccess()
    }

    // error dari ViewModel (API error)
    LaunchedEffect(state.error) {
        if (state.error != null) errorText = state.error
    }

    Box(modifier = Modifier.fillMaxSize()) {
        Image(
            painter = painterResource(id = R.drawable.login),
            contentDescription = "Background",
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop
        )

        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(top = 400.dp)
                .padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(
                text = "Masuk",
                fontSize = 22.sp,
                fontWeight = FontWeight.Bold,
                fontFamily = poppins,
                color = Color(0xFF256D85),
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(bottom = 16.dp)
            )

            OutlinedTextField(
                value = identifier,
                onValueChange = { identifier = it },
                label = { Text("Nama Pengguna / Email", fontFamily = poppins) },
                singleLine = true,
                shape = RoundedCornerShape(12.dp),
                enabled = !state.loading,
                colors = TextFieldDefaults.colors(
                    focusedContainerColor = Color(0xFFF5F5F5),
                    unfocusedContainerColor = Color(0xFFF5F5F5),
                    focusedIndicatorColor = Color(0xFF256D85),
                    unfocusedIndicatorColor = Color.Gray,
                    cursorColor = Color(0xFF256D85)
                ),
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(Modifier.height(20.dp))

            OutlinedTextField(
                value = password,
                onValueChange = { password = it },
                label = { Text("Kata sandi", fontFamily = poppins) },
                singleLine = true,
                shape = RoundedCornerShape(12.dp),
                enabled = !state.loading,
                colors = TextFieldDefaults.colors(
                    focusedContainerColor = Color(0xFFF5F5F5),
                    unfocusedContainerColor = Color(0xFFF5F5F5),
                    focusedIndicatorColor = Color(0xFF256D85),
                    unfocusedIndicatorColor = Color.Gray,
                    cursorColor = Color(0xFF256D85)
                ),
                modifier = Modifier.fillMaxWidth(),
                visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                trailingIcon = {
                    IconButton(
                        enabled = !state.loading,
                        onClick = { passwordVisible = !passwordVisible }
                    ) {
                        Icon(
                            painter = painterResource(
                                id = if (passwordVisible) R.drawable.show else R.drawable.hide
                            ),
                            contentDescription = if (passwordVisible) "Sembunyikan password" else "Tampilkan password",
                            modifier = Modifier.size(28.dp),
                            tint = Color.Gray
                        )
                    }
                }
            )

            Spacer(Modifier.height(6.dp))

            Text(
                text = "Lupa kata sandi?",
                fontSize = 12.sp,
                fontFamily = poppins,
                color = Color.Gray,
                textAlign = TextAlign.End,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(end = 4.dp)
                    .clickable(enabled = !state.loading) { onForgotPasswordClicked() }
            )

            errorText?.let {
                Spacer(Modifier.height(8.dp))
                Text(
                    text = it,
                    color = Color.Red,
                    fontSize = 12.sp,
                    textAlign = TextAlign.Center,
                    modifier = Modifier.fillMaxWidth()
                )
            }

            Spacer(Modifier.height(20.dp))

            Button(
                onClick = {
                    val email = identifier.trim()
                    val pass = password.trim()

                    when {
                        email.isBlank() || pass.isBlank() ->
                            errorText = "Isi username/email dan password dulu."
                        pass.length < 6 ->
                            errorText = "Password minimal 6 karakter."
                        !email.contains("@") ->
                            errorText = "Untuk login saat ini, masukkan email yang valid."
                        else -> {
                            errorText = null
                            vm.login(email, pass) // ✅ panggil API
                        }
                    }
                },
                enabled = !state.loading,
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFFA06B)),
                shape = RoundedCornerShape(10.dp),
                modifier = Modifier
                    .width(270.dp)
                    .height(48.dp)
                    .align(Alignment.CenterHorizontally)
            ) {
                if (state.loading) {
                    CircularProgressIndicator(
                        strokeWidth = 2.dp,
                        modifier = Modifier.size(18.dp),
                        color = Color.White
                    )
                    Spacer(Modifier.width(10.dp))
                }
                Text(
                    text = if (state.loading) "Memproses..." else "Konfirmasi",
                    color = Color.White,
                    fontFamily = poppins,
                    fontWeight = FontWeight.SemiBold,
                    fontSize = 15.sp
                )
            }

            Spacer(Modifier.height(16.dp))

            Row(
                horizontalArrangement = Arrangement.Center,
                modifier = Modifier.fillMaxWidth()
            ) {
                Text(
                    text = "Belum punya akun?",
                    fontSize = 10.sp,
                    fontFamily = poppins,
                    color = Color.Black
                )
                Spacer(Modifier.width(4.dp))
                Text(
                    text = "Daftar",
                    fontSize = 10.sp,
                    fontFamily = poppins,
                    fontWeight = FontWeight.Bold,
                    color = Color.Blue,
                    textDecoration = TextDecoration.Underline,
                    modifier = Modifier.clickable(enabled = !state.loading) { onRegisterClicked() }
                )
            }
        }
    }
}

@Preview(showSystemUi = true, showBackground = true)
@Composable
fun LoginScreenPreview() {
    MaterialTheme {
        // Preview tidak pakai VM beneran (biar aman)
        // Kalau mau preview tetap, bikin dummy wrapper sendiri.
    }
}
