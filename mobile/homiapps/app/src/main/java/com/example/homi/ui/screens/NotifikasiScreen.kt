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
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Campaign
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material.icons.filled.Payments
import androidx.compose.material.icons.filled.Warning
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.NotificationItem
import com.example.homi.ui.viewmodel.NotificationViewModel

private val BlueMain     = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFE26A2C)
private val LineGray     = Color(0xFFE6E6E6)
private val TextPrimary  = Color(0xFF0E0E0E)

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

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        Column(Modifier.fillMaxSize()) {

            // ===== HEADER (judul bener-bener center + kanan fixed width) =====
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 12.dp)
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(44.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Box(
                        modifier = Modifier.width(48.dp),
                        contentAlignment = Alignment.CenterStart
                    ) {
                        Icon(
                            imageVector = Icons.Default.ArrowBack,
                            contentDescription = "Back",
                            tint = Color.White,
                            modifier = Modifier
                                .size(22.dp)
                                .clickable { onBack() }
                        )
                    }

                    Box(
                        modifier = Modifier.weight(1f),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = "Notifikasi",
                            color = Color.White,
                            fontFamily = PoppinsSemi,
                            fontSize = 16.sp,
                            textAlign = TextAlign.Center
                        )
                    }

                    Box(
                        modifier = Modifier.width(88.dp),
                        contentAlignment = Alignment.CenterEnd
                    ) {
                        Text(
                            text = "Baca Semua",
                            color = Color.White,
                            fontFamily = PoppinsReg,
                            fontSize = 13.sp,
                            modifier = Modifier.clickable {
                                vm.readAll()
                                vm.refreshUnreadCount()
                            }
                        )
                    }
                }

                Spacer(Modifier.height(6.dp))

                // ✅ ini yang kamu minta: deskripsi center
                Text(
                    text = "Pantau informasi penting seperti pengumuman, iuran, dan peringatan.\nKetuk notifikasi untuk melihat detail.",
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = Color.White.copy(alpha = 0.92f),
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center,
                    lineHeight = 16.sp
                )

                Spacer(Modifier.height(10.dp))
            }

            // ===== Konten putih rounded =====
            Card(
                modifier = Modifier.fillMaxSize(),
                shape = RoundedCornerShape(topStart = 28.dp, topEnd = 28.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White)
            ) {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 16.dp, vertical = 14.dp)
                ) {
                    when {
                        state.loading -> CircularProgressIndicator(Modifier.align(Alignment.Center))

                        state.error != null -> {
                            Column(
                                modifier = Modifier.align(Alignment.Center),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text(
                                    text = state.error ?: "Terjadi kesalahan",
                                    fontFamily = PoppinsReg,
                                    color = Color.Red,
                                    fontSize = 13.sp,
                                    textAlign = TextAlign.Center
                                )
                                Spacer(Modifier.height(10.dp))
                                Button(onClick = { vm.refresh() }) {
                                    Text("Coba lagi", fontFamily = PoppinsSemi)
                                }
                            }
                        }

                        state.items.isEmpty() -> {
                            Column(
                                modifier = Modifier.align(Alignment.Center),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Icon(
                                    imageVector = Icons.Default.Notifications,
                                    contentDescription = null,
                                    tint = Color(0xFF9AA0A6),
                                    modifier = Modifier.size(42.dp)
                                )
                                Spacer(Modifier.height(10.dp))
                                Text(
                                    text = "Belum ada notifikasi",
                                    fontFamily = PoppinsReg,
                                    color = Color(0xFF777777),
                                    fontSize = 13.sp
                                )
                            }
                        }

                        else -> {
                            LazyColumn(
                                modifier = Modifier.fillMaxSize(),
                                verticalArrangement = Arrangement.spacedBy(10.dp),
                                contentPadding = PaddingValues(bottom = 16.dp)
                            ) {
                                items(state.items, key = { it.id }) { item ->
                                    NotifListItem(
                                        item = item,
                                        onClick = { selected = item }
                                    )
                                }
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
                containerColor = Color.White
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
    val titleText = item.title.ifBlank { "-" }
    val bodyText  = item.body?.ifBlank { "-" } ?: "-"
    val dateText  = item.createdAt?.ifBlank { "-" } ?: "-"

    val icon = when (item.type?.lowercase()) {
        "risk_warning", "warning" -> Icons.Default.Warning
        "payment", "fee", "iuran" -> Icons.Default.Payments
        "announcement", "pengumuman" -> Icons.Default.Campaign
        else -> Icons.Default.Notifications
    }

    val shape = RoundedCornerShape(16.dp)

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clip(shape)
            .border(1.dp, LineGray, shape)
            .clickable { onClick() }
            .padding(12.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Box(
            modifier = Modifier
                .size(44.dp)
                .clip(CircleShape)
                .background(if (item.isRead) Color(0xFFF2F2F2) else AccentOrange.copy(alpha = 0.18f)),
            contentAlignment = Alignment.Center
        ) {
            Icon(
                imageVector = icon,
                contentDescription = null,
                tint = if (item.isRead) Color(0xFF6C6C6C) else AccentOrange
            )
        }

        Spacer(Modifier.width(12.dp))

        Column(Modifier.weight(1f)) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Text(
                    text = titleText,
                    fontFamily = PoppinsSemi,
                    fontSize = 14.sp,
                    color = TextPrimary,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis,
                    modifier = Modifier.weight(1f)
                )

                if (!item.isRead) {
                    Spacer(Modifier.width(8.dp))
                    Box(
                        modifier = Modifier
                            .size(8.dp)
                            .clip(CircleShape)
                            .background(AccentOrange)
                    )
                }
            }

            Spacer(Modifier.height(4.dp))

            Text(
                text = bodyText,
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color(0xFF444444),
                maxLines = 2,
                overflow = TextOverflow.Ellipsis
            )

            Spacer(Modifier.height(6.dp))

            Text(
                text = dateText,
                fontFamily = PoppinsReg,
                fontSize = 10.sp,
                color = Color(0xFF888888)
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
    val titleText = item.title.ifBlank { "-" }
    val bodyText  = item.body?.ifBlank { "-" } ?: "-"
    val dateText  = item.createdAt?.ifBlank { "-" } ?: "-"
    val typeText  = item.type?.ifBlank { "-" } ?: "-"
    val metaText  = formatMeta(item.meta)

    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 18.dp, vertical = 10.dp)
    ) {
        Box(
            modifier = Modifier
                .align(Alignment.CenterHorizontally)
                .width(44.dp)
                .height(5.dp)
                .clip(RoundedCornerShape(99.dp))
                .background(Color(0xFFE0E0E0))
        )

        Spacer(Modifier.height(14.dp))

        Text(
            text = titleText,
            fontFamily = PoppinsSemi,
            fontSize = 16.sp,
            color = TextPrimary
        )

        Spacer(Modifier.height(10.dp))

        Row(verticalAlignment = Alignment.CenterVertically) {
            Box(
                modifier = Modifier
                    .clip(RoundedCornerShape(999.dp))
                    .background(AccentOrange.copy(alpha = 0.12f))
                    .border(1.dp, AccentOrange.copy(alpha = 0.35f), RoundedCornerShape(999.dp))
                    .padding(horizontal = 10.dp, vertical = 6.dp)
            ) {
                Text(
                    text = typeText,
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = AccentOrange
                )
            }

            Spacer(Modifier.width(10.dp))

            Text(
                text = dateText,
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color(0xFF777777)
            )
        }

        Spacer(Modifier.height(14.dp))

        Text("Isi", fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextPrimary)
        Spacer(Modifier.height(6.dp))
        Text(
            text = bodyText,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = Color(0xFF333333),
            lineHeight = 18.sp
        )

        if (metaText.isNotBlank() && metaText != "-") {
            Spacer(Modifier.height(14.dp))
            Text("Detail", fontFamily = PoppinsSemi, fontSize = 13.sp, color = TextPrimary)
            Spacer(Modifier.height(6.dp))
            Text(
                text = metaText,
                fontFamily = PoppinsReg,
                fontSize = 13.sp,
                color = Color(0xFF333333),
                lineHeight = 18.sp
            )
        }

        Spacer(Modifier.height(18.dp))

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(10.dp)
        ) {
            OutlinedButton(
                modifier = Modifier.weight(1f),
                onClick = onClose,
                border = BorderStroke(1.dp, LineGray)
            ) {
                Text("Tutup", fontFamily = PoppinsSemi, color = TextPrimary)
            }

            Button(
                modifier = Modifier.weight(1f),
                onClick = { if (!item.isRead) onMarkRead() else onClose() },
                colors = ButtonDefaults.buttonColors(
                    containerColor = if (item.isRead) Color(0xFFBDBDBD) else AccentOrange
                )
            ) {
                Text(
                    text = if (item.isRead) "Sudah dibaca" else "Tandai dibaca",
                    fontFamily = PoppinsSemi,
                    color = Color.White
                )
            }
        }

        Spacer(Modifier.height(18.dp))
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
