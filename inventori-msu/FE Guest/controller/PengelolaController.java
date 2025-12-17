package com.Habb.InventarisMSU.controller;

import com.Habb.InventarisMSU.model.ItemType;
import com.Habb.InventarisMSU.service.ItemService;
import com.Habb.InventarisMSU.service.PeminjamanService;
import com.Habb.InventarisMSU.repository.PeminjamanRepository;
import com.Habb.InventarisMSU.model.PeminjamanStatus;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

@Controller
@RequestMapping("/pengelola")
public class PengelolaController {

    private final ItemService itemService;
    private final PeminjamanRepository peminjamanRepository;
    private final PeminjamanService peminjamanService;

    public PengelolaController(ItemService itemService, PeminjamanRepository peminjamanRepository,
            PeminjamanService peminjamanService) {
        this.itemService = itemService;
        this.peminjamanRepository = peminjamanRepository;
        this.peminjamanService = peminjamanService;
    }

    @GetMapping("/beranda")
    public String beranda(Model model) {
        var all = itemService.getAllItems();

        var barangList = all.stream()
                .filter(it -> it.getType() == ItemType.BARANG)
                .toList();

        var ruanganList = all.stream()
                .filter(it -> it.getType() == ItemType.RUANGAN)
                .toList();

        model.addAttribute("barangList", barangList);
        model.addAttribute("ruanganList", ruanganList);
        return "pengelola/beranda";
    }

    @GetMapping("/approval")
    public String approval(Model model) {
        var pendingList = peminjamanRepository.findByStatus(com.Habb.InventarisMSU.model.PeminjamanStatus.PENDING);
        var historyList = peminjamanRepository.findByStatusNot(com.Habb.InventarisMSU.model.PeminjamanStatus.PENDING);
        model.addAttribute("pendingList", pendingList);
        model.addAttribute("historyList", historyList);
        return "pengelola/approval";
    }

    @PostMapping("/approval/update")
    public String updateStatus(@RequestParam("id") Long id, @RequestParam("status") String statusStr,
            @RequestParam(value = "reason", required = false) String reason) {
        PeminjamanStatus status = PeminjamanStatus.valueOf(statusStr);
        peminjamanService.updateStatus(id, status);
        // We can add logic to save reason later if needed, Peminjaman model has
        // 'reason' field but it seems to be for borrower's reason.
        // If we want to store rejection reason, we might need a separate field or reuse
        // 'reason' if it's empty, or just log it for now.
        return "redirect:/pengelola/approval";
    }

    @GetMapping("/laporan")
    public String laporan() {
        return "pengelola/laporan";
    }
}
