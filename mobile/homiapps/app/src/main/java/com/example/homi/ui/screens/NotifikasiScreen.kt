package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Campaign
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material.icons.filled.Payments
import androidx.compose.material.icons.filled.Warning
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.NotificationItem
import com.example.homi.ui.viewmodel.NotificationViewModel

private val BlueMain     = Color(0xFF2F7FA3)
private val BlueDark     = Color(0xFF1A5E7B)
private val AccentOrange = Color(0xFFF7A477)
private val LineGray     = Color(0xFFE6E6E6)
private val TextPrimary  = Color(0xFF0E0E0E)
private val HintGray     = Color(0xFF8A8A8A)
private val ErrorRed     = Color(0xFFEF4444)
private val SuccessGreen = Color(0xFF22C55E)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun NotifikasiScreen(
    vm: NotificationViewModel,
    onBack: () -> Unit
) {
    val state by vm.state.collectAsState()

    var selected by remember { mutableStateOf<NotificationItem?>(null) }
    val sheetState = rememberModalBottomSheetState(skipPartiallyExpanded = true)

    LaunchedEffect(Unit) { vm.refresh() }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Brush.verticalGradient(listOf(BlueMain, BlueDark)))
    ) {
        // ===== PREMIUM GRADIENT TOPPER =====
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
                    text = "Notifikasi",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )

                TextButton(
                    onClick = {
                        vm.readAll()
                        vm.refreshUnreadCount()
                    },
                    modifier = Modifier.align(Alignment.CenterEnd)
                ) {
                    Text(
                        "Baca Semua",
                        fontFamily = PoppinsSemi,
                        fontSize = 12.sp,
                        color = Color.White.copy(alpha = 0.9f)
                    )
                }
            }

            Spacer(Modifier.height(8.dp))

            Box(
                modifier = Modifier
                    .size(52.dp)
                    .background(Color.White.copy(alpha = 0.15f), RoundedCornerShape(16.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Default.Notifications,
                    contentDescription = null,
                    tint = Color.White,
                    modifier = Modifier.size(26.dp)
                )
            }

            Spacer(Modifier.height(8.dp))
            Text(
                text = "Tetap terinformasi dengan pembaruan terkini\ndari Hawaiian Garden.",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                lineHeight = 16.sp
            )
        }

        // ===== CONTENT AREA =====
        Surface(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            color = Color.White
        ) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(top = 8.dp)
            ) {
                when {
                    state.loading -> CircularProgressIndicator(
                        modifier = Modifier.align(Alignment.Center),
                        color = BlueMain,
                        strokeWidth = 3.dp
                    )

                    state.error != null -> {
                        Column(
                            modifier = Modifier.align(Alignment.Center).padding(32.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Icon(
                                imageVector = Icons.Default.Warning,
                                contentDescription = null,
                                tint = ErrorRed,
                                modifier = Modifier.size(48.dp)
                            )
                            Spacer(Modifier.height(16.dp))
                            Text(
                                text = state.error ?: "Koneksi terganggu",
                                fontFamily = PoppinsSemi,
                                fontSize = 15.sp,
                                textAlign = TextAlign.Center
                            )
                            Spacer(Modifier.height(12.dp))
                            Button(
                                onClick = { vm.refresh() },
                                colors = ButtonDefaults.buttonColors(containerColor = BlueMain),
                                shape = RoundedCornerShape(12.dp)
                            ) {
                                Text("Coba Lagi", fontFamily = PoppinsSemi)
                            }
                        }
                    }

                    state.items.isEmpty() -> {
                        Column(
                            modifier = Modifier.align(Alignment.Center),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Surface(
                                shape = CircleShape,
                                color = Color(0xFFF1F5F9),
                                modifier = Modifier.size(100.dp)
                            ) {
                                Box(contentAlignment = Alignment.Center) {
                                    Icon(
                                        imageVector = Icons.Default.Notifications,
                                        contentDescription = null,
                                        tint = HintGray,
                                        modifier = Modifier.size(48.dp)
                                    )
                                }
                            }
                            Spacer(Modifier.height(20.dp))
                            Text(
                                text = "Semua Sudah Terbaca",
                                fontFamily = PoppinsSemi,
                                color = TextPrimary,
                                fontSize = 16.sp
                            )
                            Text(
                                text = "Belum ada notifikasi baru untuk Anda.",
                                fontFamily = PoppinsReg,
                                color = HintGray,
                                fontSize = 13.sp
                            )
                        }
                    }

                    else -> {
                        LazyColumn(
                            modifier = Modifier.fillMaxSize(),
                            verticalArrangement = Arrangement.spacedBy(1.dp), // Divider effect
                            contentPadding = PaddingValues(bottom = 32.dp)
                        ) {
                            items(state.items, key = { it.id }) { item ->
                                NotifListItem(
                                    item = item,
                                    onClick = { selected = item }
                                )
                                HorizontalDivider(
                                    color = Color(0xFFF1F5F9),
                                    thickness = 1.dp,
                                    modifier = Modifier.padding(horizontal = 24.dp)
                                )
                            }
                        }
                    }
                }
            }
        }

        if (selected != null) {
            ModalBottomSheet(
                onDismissRequest = { selected = null },
                sheetState = sheetState,
                containerColor = Color.White,
                dragHandle = { BottomSheetDefaults.DragHandle(color = Color(0xFFE2E8F0)) }
            ) {
                NotificationDetailSheet(
                    item = selected!!,
                    onMarkRead = {
                        vm.markRead(selected!!.id)
                        vm.refreshUnreadCount()
                        selected = selected!!.copy(isRead = true)
                    },
                    onClose = { selected = null }
                )
            }
        }
    }
}

@Composable
private fun NotifListItem(
    item: NotificationItem,
    onClick: () -> Unit
) {
    val titleText = item.title.ifBlank { "Informasi Baru" }
    val bodyText  = item.body?.ifBlank { "Ketuk untuk melihat detail informasi." } ?: "Ketuk untuk melihat detail."
    val dateText  = item.createdAt ?: "Baru saja"

    val (icon, color) = when (item.type?.lowercase()) {
        "warning", "hazard", "risk_warning" -> Icons.Default.Warning to ErrorRed
        "payment", "fee", "bill" -> Icons.Default.Payments to SuccessGreen
        "announcement", "info" -> Icons.Default.Campaign to BlueMain
        else -> Icons.Default.Notifications to BlueMain
    }

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() }
            .background(if (item.isRead) Color.White else BlueMain.copy(alpha = 0.03f))
            .padding(horizontal = 24.dp, vertical = 20.dp),
        verticalAlignment = Alignment.Top
    ) {
        Surface(
            shape = CircleShape,
            color = if (item.isRead) Color(0xFFF8FAFC) else color.copy(alpha = 0.12f),
            modifier = Modifier.size(48.dp)
        ) {
            Box(contentAlignment = Alignment.Center) {
                Icon(
                    imageVector = icon,
                    contentDescription = null,
                    tint = if (item.isRead) HintGray else color,
                    modifier = Modifier.size(22.dp)
                )
            }
        }

        Spacer(Modifier.width(16.dp))

        Column(Modifier.weight(1f)) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = if (item.isRead) titleText else "● $titleText",
                    fontFamily = if (item.isRead) PoppinsReg else PoppinsSemi,
                    fontSize = 15.sp,
                    color = if (item.isRead) TextPrimary.copy(alpha = 0.8f) else BlueMain,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis,
                    modifier = Modifier.weight(1f)
                )
                
                Text(
                    text = dateText,
                    fontFamily = PoppinsReg,
                    fontSize = 11.sp,
                    color = HintGray
                )
            }

            Spacer(Modifier.height(4.dp))

            Text(
                text = bodyText,
                fontFamily = PoppinsReg,
                fontSize = 13.sp,
                color = if (item.isRead) Color(0xFF64748B) else TextPrimary,
                maxLines = 2,
                overflow = TextOverflow.Ellipsis,
                lineHeight = 18.sp
            )
        }
    }
}

@Composable
private fun NotificationDetailSheet(
    item: NotificationItem,
    onMarkRead: () -> Unit,
    onClose: () -> Unit
) {
    val titleText = item.title.ifBlank { "Detail Notifikasi" }
    val bodyText  = item.body?.ifBlank { "Tidak ada detail tambahan." } ?: "Tidak ada detail."
    val typeText  = (item.type ?: "Info").replace("_", " ").uppercase()

    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 24.dp)
            .padding(bottom = 32.dp)
    ) {
        Surface(
            shape = RoundedCornerShape(8.dp),
            color = BlueMain.copy(alpha = 0.1f),
            modifier = Modifier.padding(bottom = 16.dp)
        ) {
            Text(
                text = typeText,
                fontFamily = PoppinsSemi,
                fontSize = 11.sp,
                color = BlueMain,
                modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
            )
        }

        Text(
            text = titleText,
            fontFamily = PoppinsSemi,
            fontSize = 20.sp,
            color = TextPrimary,
            lineHeight = 26.sp
        )

        Spacer(Modifier.height(8.dp))

        Text(
            text = item.createdAt ?: "",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = HintGray
        )

        Spacer(Modifier.height(24.dp))

        Text(
            text = bodyText,
            fontFamily = PoppinsReg,
            fontSize = 15.sp,
            color = TextPrimary,
            lineHeight = 24.sp
        )

        Spacer(Modifier.height(32.dp))

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            Button(
                modifier = Modifier
                    .weight(1f)
                    .height(50.dp),
                onClick = {
                    if (!item.isRead) onMarkRead()
                    onClose()
                },
                colors = ButtonDefaults.buttonColors(
                    containerColor = BlueMain
                ),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text(
                    text = if (item.isRead) "Tutup" else "Tandai Sudah Baca",
                    fontFamily = PoppinsSemi,
                    color = Color.White
                )
            }
        }
    }
}

private fun formatMeta(meta: Any?): String {
    if (meta == null) return "-"
    return when (meta) {
        is Map<*, *> -> if (meta.isEmpty()) "-" else meta.entries.joinToString("\n") { (k, v) ->
            "${k.toString()}: ${v?.toString() ?: "-"}"
        }
        is List<*> -> if (meta.isEmpty()) "-" else meta.joinToString("\n") { "- ${it?.toString() ?: "-"}" }
        else -> meta.toString()
    }
}
