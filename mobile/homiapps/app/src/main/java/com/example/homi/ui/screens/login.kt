package com.example.homi.ui.screens

import android.app.Activity
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Image
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
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
import com.google.android.gms.auth.api.signin.GoogleSignIn
import com.google.android.gms.auth.api.signin.GoogleSignInOptions
import com.google.android.gms.common.api.ApiException
import kotlin.math.max
import kotlin.math.min

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LoginScreen(
    vm: AuthViewModel,
    onLoginSuccess: () -> Unit,
    onRegisterClicked: () -> Unit = {},
    onForgotPasswordClicked: () -> Unit = {}
) {
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val state by vm.state.collectAsState()

    var identifier by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var passwordVisible by remember { mutableStateOf(false) }
    var errorText by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(state.isLoggedIn) {
        if (state.isLoggedIn) onLoginSuccess()
    }
    LaunchedEffect(state.error) {
        if (state.error != null) errorText = state.error
    }

    // ===================== GOOGLE SIGN IN SETUP =====================
    val context = LocalContext.current
    val activity = context as Activity

    val webClientId = context.getString(R.string.google_web_client_id)

    val gso = remember(webClientId) {
        GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
            .requestEmail()
            .requestIdToken(webClientId)
            .build()
    }

    val googleClient = remember { GoogleSignIn.getClient(activity, gso) }

    val googleLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.StartActivityForResult()
    ) { result ->
        val task = GoogleSignIn.getSignedInAccountFromIntent(result.data)
        try {
            val account = task.getResult(ApiException::class.java)
            val idToken = account.idToken

            if (idToken.isNullOrBlank()) {
                errorText = "Gagal mengambil token Google (idToken kosong)."
            } else {
                errorText = null
                vm.loginGoogle(idToken)
            }
        } catch (e: Exception) {
            errorText = e.message ?: "Login Google dibatalkan / gagal."
        }
    }
    // ================================================================

    BoxWithConstraints(modifier = Modifier.fillMaxSize()) {
        Image(
            painter = painterResource(id = R.drawable.login),
            contentDescription = "Background",
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop
        )

        val topPad = run {
            val raw = (maxHeight.value * 0.48f).dp
            min(420f, max(260f, raw.value)).dp
        }

        val scroll = rememberScrollState()

        Column(
            modifier = Modifier
                .fillMaxSize()
                .imePadding()
                .verticalScroll(scroll)
                .padding(horizontal = 24.dp)
                .padding(top = topPad, bottom = 24.dp),
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
                label = { Text("Email", fontFamily = poppins) },
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

            Spacer(Modifier.height(14.dp))

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
                visualTransformation =
                    if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                trailingIcon = {
                    IconButton(
                        enabled = !state.loading,
                        onClick = { passwordVisible = !passwordVisible }
                    ) {
                        Icon(
                            painter = painterResource(
                                id = if (passwordVisible) R.drawable.show else R.drawable.hide
                            ),
                            contentDescription = null,
                            modifier = Modifier.size(24.dp),
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
                Spacer(Modifier.height(10.dp))
                Text(
                    text = it,
                    color = Color.Red,
                    fontSize = 12.sp,
                    textAlign = TextAlign.Center,
                    modifier = Modifier.fillMaxWidth()
                )
            }

            Spacer(Modifier.height(18.dp))

            Button(
                onClick = {
                    val email = identifier.trim()
                    val pass = password.trim()
                    when {
                        email.isBlank() || pass.isBlank() ->
                            errorText = "Isi email dan password dulu."
                        pass.length < 6 ->
                            errorText = "Password minimal 6 karakter."
                        !email.contains("@") ->
                            errorText = "Masukkan email yang valid."
                        else -> {
                            errorText = null
                            vm.login(email, pass)
                        }
                    }
                },
                enabled = !state.loading,
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFFA06B)),
                shape = RoundedCornerShape(12.dp),
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp)
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
                    text = if (state.loading) "Memproses..." else "Masuk",
                    color = Color.White,
                    fontFamily = poppins,
                    fontWeight = FontWeight.SemiBold,
                    fontSize = 15.sp
                )
            }

            Spacer(Modifier.height(14.dp))

            // ✅ tombol google profesional (tanpa ElevatedCard)
            OutlinedButton(
                onClick = {
                    errorText = null
                    googleClient.signOut() // biar selalu pilih akun
                    googleLauncher.launch(googleClient.signInIntent)
                },
                enabled = !state.loading,
                shape = RoundedCornerShape(12.dp),
                border = ButtonDefaults.outlinedButtonBorder.copy(width = 1.dp),
                colors = ButtonDefaults.outlinedButtonColors(
                    containerColor = Color.White,
                    contentColor = Color(0xFF222222)
                ),
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp)
            ) {
                Image(
                    painter = painterResource(id = R.drawable.ic_google),
                    contentDescription = null,
                    modifier = Modifier.size(20.dp)
                )
                Spacer(Modifier.width(10.dp))
                Text(
                    text = "Lanjutkan dengan Google",
                    fontFamily = poppins,
                    fontWeight = FontWeight.SemiBold,
                    fontSize = 14.sp
                )
            }

            Spacer(Modifier.height(14.dp))

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

            Spacer(Modifier.height(18.dp))
        }
    }
}

@Preview(showSystemUi = true, showBackground = true)
@Composable
fun LoginScreenPreview() {
    MaterialTheme { }
}
