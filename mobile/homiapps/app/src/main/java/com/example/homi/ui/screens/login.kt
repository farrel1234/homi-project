package com.example.homi.ui.screens

import android.app.Activity
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
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

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LoginScreen(
    vm: AuthViewModel,
    onLoginSuccess: () -> Unit,
    onRegisterClicked: () -> Unit = {},
    onForgotPasswordClicked: () -> Unit = {},
    onGoGoogleRegister: (email: String, name: String, gid: String) -> Unit = { _, _, _ -> }
) {
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val state by vm.state.collectAsState()

    var identifier by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var tenantCode by remember { mutableStateOf(state.tenantCode) }
    var tenantExpanded by remember { mutableStateOf(false) }
    var passwordVisible by remember { mutableStateOf(false) }
    var errorText by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(state.isLoggedIn) {
        if (state.isLoggedIn) onLoginSuccess()
    }
    LaunchedEffect(state.error) {
        if (state.error != null) errorText = state.error
    }
    LaunchedEffect(state.tenantCode) {
        if (state.tenantCode.isNotBlank() && state.tenantCode != tenantCode) {
            tenantCode = state.tenantCode
        }
    }

    LaunchedEffect(state.needsGoogleRegister) {
        if (state.needsGoogleRegister) {
            onGoGoogleRegister(state.googleEmail, state.googleName, state.googleId)
            vm.clearGoogleRegister()
        }
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
                .imePadding()
        ) {
            // Flexible space at top to push content down
            Spacer(modifier = Modifier.weight(1f))

            // Login Sheet
            Surface(
                modifier = Modifier
                    .fillMaxWidth()
                    .clip(RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp)),
                color = Color.White.copy(alpha = 0.95f),
                tonalElevation = 8.dp
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .verticalScroll(rememberScrollState())
                        .padding(24.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Text(
                        text = "Masuk",
                        fontSize = 24.sp,
                        fontWeight = FontWeight.Bold,
                        fontFamily = poppins,
                        color = Color(0xFF256D85),
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(bottom = 20.dp)
                    )

                    // Dropdown Perumahan
                    ExposedDropdownMenuBox(
                        expanded = tenantExpanded,
                        onExpandedChange = { tenantExpanded = !tenantExpanded },
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        OutlinedTextField(
                            value = state.tenants.find { it.code == tenantCode }?.name ?: tenantCode,
                            onValueChange = {},
                            readOnly = true,
                            label = { Text("Pilih Perumahan", fontFamily = poppins) },
                            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = tenantExpanded) },
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = Color(0xFF256D85),
                                unfocusedBorderColor = Color.Gray,
                                focusedContainerColor = Color(0xFFF8F8F8),
                                unfocusedContainerColor = Color(0xFFF8F8F8)
                            ),
                            shape = RoundedCornerShape(12.dp),
                            modifier = Modifier
                                .menuAnchor()
                                .fillMaxWidth()
                        )

                        ExposedDropdownMenu(
                            expanded = tenantExpanded,
                            onDismissRequest = { tenantExpanded = false },
                            modifier = Modifier.background(Color.White)
                        ) {
                            if (state.tenants.isEmpty()) {
                                DropdownMenuItem(
                                    text = { Text("Memuat...", fontFamily = poppins) },
                                    onClick = { tenantExpanded = false }
                                )
                            }
                            state.tenants.forEach { t ->
                                DropdownMenuItem(
                                    text = { 
                                        Column {
                                            Text(t.name, fontFamily = poppins, fontWeight = FontWeight.Bold)
                                            Text(t.code, fontSize = 10.sp, color = Color.Gray)
                                        }
                                    },
                                    onClick = {
                                        tenantCode = t.code
                                        tenantExpanded = false
                                        errorText = null
                                    }
                                )
                            }
                        }
                    }

                    Spacer(Modifier.height(16.dp))

                    OutlinedTextField(
                        value = identifier,
                        onValueChange = { identifier = it },
                        label = { Text("Email", fontFamily = poppins) },
                        singleLine = true,
                        shape = RoundedCornerShape(12.dp),
                        enabled = !state.loading,
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = Color(0xFF256D85),
                            unfocusedBorderColor = Color.Gray,
                            focusedContainerColor = Color(0xFFF8F8F8),
                            unfocusedContainerColor = Color(0xFFF8F8F8)
                        ),
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(Modifier.height(16.dp))

                    OutlinedTextField(
                        value = password,
                        onValueChange = { password = it },
                        label = { Text("Kata sandi", fontFamily = poppins) },
                        singleLine = true,
                        shape = RoundedCornerShape(12.dp),
                        enabled = !state.loading,
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = Color(0xFF256D85),
                            unfocusedBorderColor = Color.Gray,
                            focusedContainerColor = Color(0xFFF8F8F8),
                            unfocusedContainerColor = Color(0xFFF8F8F8)
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

                    Spacer(Modifier.height(8.dp))

                    Text(
                        text = "Lupa kata sandi?",
                        fontSize = 13.sp,
                        fontFamily = poppins,
                        color = Color(0xFF256D85),
                        textAlign = TextAlign.End,
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(end = 4.dp)
                            .clickable(enabled = !state.loading) { onForgotPasswordClicked() }
                    )

                    errorText?.let {
                        Spacer(Modifier.height(12.dp))
                        Text(
                            text = it,
                            color = Color.Red,
                            fontSize = 13.sp,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.fillMaxWidth()
                        )
                    }

                    Spacer(Modifier.height(24.dp))

                    Button(
                        onClick = {
                            val tenant = tenantCode.trim()
                            val email = identifier.trim()
                            val pass = password.trim()
                            when {
                                tenant.isBlank() -> errorText = "Isi kode perumahan dulu."
                                email.isBlank() || pass.isBlank() -> errorText = "Isi email dan password dulu."
                                pass.length < 6 -> errorText = "Password minimal 6 karakter."
                                !email.contains("@") -> errorText = "Masukkan email yang valid."
                                else -> {
                                    errorText = null
                                    vm.setTenantCode(tenant)
                                    vm.login(email, pass)
                                }
                            }
                        },
                        enabled = !state.loading,
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFFA06B)),
                        shape = RoundedCornerShape(12.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(54.dp)
                    ) {
                        if (state.loading) {
                            CircularProgressIndicator(
                                strokeWidth = 2.dp,
                                modifier = Modifier.size(20.dp),
                                color = Color.White
                            )
                            Spacer(Modifier.width(10.dp))
                        }
                        Text(
                            text = if (state.loading) "Memproses..." else "Masuk",
                            color = Color.White,
                            fontFamily = poppins,
                            fontWeight = FontWeight.Bold,
                            fontSize = 16.sp
                        )
                    }

                    Spacer(Modifier.height(16.dp))

                    OutlinedButton(
                        onClick = {
                            val tenant = tenantCode.trim()
                            if (tenant.isBlank()) {
                                errorText = "Isi kode perumahan dulu sebelum login Google."
                                return@OutlinedButton
                            }
                            vm.setTenantCode(tenant)
                            errorText = null
                            googleClient.signOut()
                            googleLauncher.launch(googleClient.signInIntent)
                        },
                        enabled = !state.loading,
                        shape = RoundedCornerShape(12.dp),
                        border = BorderStroke(1.dp, Color.LightGray),
                        colors = ButtonDefaults.outlinedButtonColors(containerColor = Color.White),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(54.dp)
                    ) {
                        Image(
                            painter = painterResource(id = R.drawable.ic_google),
                            contentDescription = null,
                            modifier = Modifier.size(22.dp)
                        )
                        Spacer(Modifier.width(12.dp))
                        Text(
                            text = "Lanjutkan dengan Google",
                            fontFamily = poppins,
                            color = Color(0xFF222222),
                            fontSize = 15.sp
                        )
                    }

                    Spacer(Modifier.height(20.dp))

                    Row(
                        horizontalArrangement = Arrangement.Center,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(
                            text = "Belum punya akun?",
                            fontSize = 13.sp,
                            fontFamily = poppins,
                            color = Color.Black
                        )
                        Spacer(Modifier.width(6.dp))
                        Text(
                            text = "Daftar",
                            fontSize = 13.sp,
                            fontFamily = poppins,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF256D85),
                            textDecoration = TextDecoration.Underline,
                            modifier = Modifier.clickable(enabled = !state.loading) { onRegisterClicked() }
                        )
                    }

                    Spacer(Modifier.height(16.dp))
                }
            }
        }
    }
}
