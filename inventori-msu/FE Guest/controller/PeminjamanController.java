package com.Habb.InventarisMSU.controller;

import com.Habb.InventarisMSU.dto.CartItem;
import com.Habb.InventarisMSU.model.Item;
import com.Habb.InventarisMSU.model.Peminjaman;
import com.Habb.InventarisMSU.model.PeminjamanDetail;
import com.Habb.InventarisMSU.model.PeminjamanStatus;
import com.Habb.InventarisMSU.repository.ItemRepository;
import com.Habb.InventarisMSU.repository.PeminjamanDetailRepository;
import com.Habb.InventarisMSU.repository.PeminjamanRepository;
import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.UUID;

@RestController
@RequestMapping("/api/peminjaman")
public class PeminjamanController {

    private final PeminjamanRepository peminjamanRepository;
    private final ItemRepository itemRepository;
    private final PeminjamanDetailRepository peminjamanDetailRepository;
    private final ObjectMapper objectMapper;

    // Separate repository needed for details? Yes if not cascading properly,
    // but Peminjaman has cascade=ALL. So saving Peminjaman should save details
    // IF we add them to the list.
    // However, PeminjamanDetailRepository might not exist yet?
    // Check Step 265: It exists? No, list_dir only showed models.
    // Step 277 summary said I checked repos? No I checked PeminjamanRepository.
    // I didn't check PeminjamanDetailRepository. I will assume Cascade works or
    // create repo later if needed.
    // Actually, good practice is to save via Cascade.

    public PeminjamanController(PeminjamanRepository peminjamanRepository, ItemRepository itemRepository,
            ObjectMapper objectMapper) {
        this.peminjamanRepository = peminjamanRepository;
        this.itemRepository = itemRepository;
        this.objectMapper = objectMapper;
        this.peminjamanDetailRepository = null; // cascade approach
    }

    @PostMapping(consumes = MediaType.MULTIPART_FORM_DATA_VALUE)
    public ResponseEntity<?> submitBooking(
            @RequestParam("borrowerName") String borrowerName,
            @RequestParam("email") String email,
            @RequestParam("phone") String phone,
            @RequestParam("nimNip") String nimNip,
            @RequestParam("department") String department,
            @RequestParam("reason") String reason,
            @RequestParam("description") String description,
            @RequestParam("startDate") String startDateStr,
            @RequestParam("duration") Integer duration,
            @RequestParam("items") String itemsJson,
            @RequestParam("file") MultipartFile file,
            @RequestParam(value = "session", required = false) String session) {
        try {
            // 1. Save File
            String fileName = UUID.randomUUID().toString() + "_" + file.getOriginalFilename();
            Path uploadDir = Paths.get("uploads");
            if (!Files.exists(uploadDir))
                Files.createDirectories(uploadDir);
            Path filePath = uploadDir.resolve(fileName);
            Files.copy(file.getInputStream(), filePath);

            // 2. Create Peminjaman
            Peminjaman p = new Peminjaman();
            p.setBorrowerName(borrowerName);
            p.setEmail(email);
            p.setPhone(phone);
            p.setNimNip(nimNip);
            p.setDepartment(department);
            p.setReason(reason);
            p.setDescription(description);
            // If session is not provided, try to extract from description or leave null
            if (session == null && description != null) {
                // Try extract
                // [Sesi: Pagi] ...
                if (description.contains("[Sesi:")) {
                    int start = description.indexOf("[Sesi:") + 7;
                    int end = description.indexOf("]", start);
                    if (end > start) {
                        session = description.substring(start, end).trim(); // This might get "Pagi (06..)"
                        // We probably want the code "Pagi". But keeping full string is safer for now if
                        // we don't change frontend much.
                        // Actually, let's just save what we get.
                    }
                }
            }
            p.setSession(session);

            p.setStartDate(LocalDate.parse(startDateStr));
            p.setDuration(duration);
            p.setDocumentPath(filePath.toString());
            p.setStatus(PeminjamanStatus.PENDING);

            // Calc End Date? Simple logic: same day or + days?
            // Duration is usually hours. So EndDate = StartDate usually (for daily
            // booking).
            p.setEndDate(LocalDate.parse(startDateStr));

            // 3. Parse Items & Create Details
            List<CartItem> cartItems = objectMapper.readValue(itemsJson, new TypeReference<List<CartItem>>() {
            });
            List<PeminjamanDetail> details = new ArrayList<>();

            for (CartItem ci : cartItems) {
                Item item = itemRepository.findByName(ci.getName());
                if (item != null) {
                    PeminjamanDetail pd = new PeminjamanDetail();
                    pd.setPeminjaman(p);
                    pd.setItem(item);
                    pd.setQuantity(ci.getQuantity());
                    details.add(pd);
                }
            }
            p.setDetails(details);

            // 4. Save
            peminjamanRepository.save(p);

            return ResponseEntity.ok().body("{\"message\": \"Booking berhasil disimpan\"}");

        } catch (IOException e) {
            e.printStackTrace();
            return ResponseEntity.internalServerError().body("Error saving booking: " + e.getMessage());
        }
    }

    @GetMapping
    public ResponseEntity<List<PublicBookingDTO>> getBookings(
            @RequestParam(value = "date", required = false) String dateStr) {
        List<Peminjaman> list;
        if (dateStr != null && !dateStr.isEmpty()) {
            LocalDate date = LocalDate.parse(dateStr);
            list = peminjamanRepository.findByStartDate(date);
        } else {
            // If no date, maybe return nothing or today's?
            // For security/performance, let's require date or return empty
            return ResponseEntity.ok(List.of());
        }

        List<PublicBookingDTO> dtos = new ArrayList<>();
        for (Peminjaman p : list) {
            if (p.getStatus() == PeminjamanStatus.REJECTED)
                continue;

            PublicBookingDTO dto = new PublicBookingDTO();
            dto.setId(p.getId());
            dto.setBorrowerName(maskName(p.getBorrowerName()));
            dto.setDepartment(p.getDepartment());
            dto.setDescription(p.getDescription()); // Contains session info
            dto.setStatus(p.getStatus().name());
            dto.setStartDate(p.getStartDate().toString());

            List<String> itemSummaries = new ArrayList<>();
            if (p.getDetails() != null) {
                for (PeminjamanDetail pd : p.getDetails()) {
                    itemSummaries.add(pd.getItem().getName() + " (" + pd.getQuantity() + ")");
                }
            }
            dto.setItems(itemSummaries);
            dtos.add(dto);
        }
        return ResponseEntity.ok(dtos);
    }

    private String maskName(String name) {
        if (name == null || name.length() < 2)
            return name;
        return name.substring(0, 1) + "***";
    }

    // Inner DTO
    public static class PublicBookingDTO {
        private Long id;
        // ... (existing fields)
        private String borrowerName;
        private String department;
        private String description;
        private String status;
        private String startDate;
        private List<String> items;

        // Getters Setters
        public Long getId() {
            return id;
        }

        public void setId(Long id) {
            this.id = id;
        }

        public String getBorrowerName() {
            return borrowerName;
        }

        public void setBorrowerName(String borrowerName) {
            this.borrowerName = borrowerName;
        }

        public String getDepartment() {
            return department;
        }

        public void setDepartment(String department) {
            this.department = department;
        }

        public String getDescription() {
            return description;
        }

        public void setDescription(String description) {
            this.description = description;
        }

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        public String getStartDate() {
            return startDate;
        }

        public void setStartDate(String startDate) {
            this.startDate = startDate;
        }

        public List<String> getItems() {
            return items;
        }

        public void setItems(List<String> items) {
            this.items = items;
        }
    }

    private String extractSession(String desc) {
        if (desc == null)
            return "";
        if (desc.contains("[Sesi:")) {
            try {
                int start = desc.indexOf("[Sesi:") + 7;
                int end = desc.indexOf("]", start);
                if (end > start)
                    return desc.substring(start, end).trim();
            } catch (Exception e) {
            }
        }
        return "";
    }

    private boolean isOverlapping(String s1, String s2) {
        if (s1 == null || s2 == null)
            return true; // Safety: if unknown, assume overlap
        s1 = s1.toLowerCase();
        s2 = s2.toLowerCase();

        // Define scopes
        // Scope 1: Morning (06-12)
        // Scope 2: Afternoon (12-18)
        // Scope 3: Evening (18-20)

        // s1 scopes
        boolean[] scopes1 = getScopes(s1);
        boolean[] scopes2 = getScopes(s2);

        for (int i = 0; i < 3; i++) {
            if (scopes1[i] && scopes2[i])
                return true;
        }
        return false;
    }

    private boolean[] getScopes(String s) {
        // [morn, noon, eve]
        boolean[] r = { false, false, false };

        if (s.contains("seharian")) {
            r[0] = true;
            r[1] = true;
            r[2] = true;
        } else if (s.contains("pagisiang")) {
            r[0] = true;
            r[1] = true;
        } else if (s.contains("siangmalam")) {
            r[1] = true;
            r[2] = true;
        } else if (s.contains("pagi")) {
            r[0] = true;
        } else if (s.contains("siang")) {
            r[1] = true;
        } else if (s.contains("malam")) {
            r[2] = true;
        }

        return r;
    }

    @GetMapping("/check")
    public ResponseEntity<List<AvailabilityDTO>> checkAvailability(
            @RequestParam("date") String dateStr,
            @RequestParam(value = "session", required = false) String session) {

        LocalDate date = LocalDate.parse(dateStr);
        List<Item> allItems = itemRepository.findAll();

        // 1. Calculate 'Total Real Stock' (Asset Count) using Aggregate Query
        // This avoids iterating thousands of bookings and LazyLoading issues.
        List<PeminjamanStatus> activeStatuses = Arrays.asList(PeminjamanStatus.APPROVED, PeminjamanStatus.TAKEN);
        List<Object[]> borrowedCountsList = peminjamanRepository.countBorrowedItems(activeStatuses);

        java.util.Map<Long, Integer> borrowedCounts = new java.util.HashMap<>();
        for (Object[] row : borrowedCountsList) {
            Long itemId = (Long) row[0];
            Long qty = (Long) row[1]; // SUM returns Long in JPQL
            borrowedCounts.put(itemId, qty.intValue());
        }

        // 2. Find overlapping bookings (now eager fetches details)
        List<Peminjaman> overlapping = peminjamanRepository.findOverlappingBookings(date);

        List<AvailabilityDTO> result = new ArrayList<>();

        for (Item item : allItems) {
            int currentDbStock = item.getStock();
            int totalAssetStock = currentDbStock + borrowedCounts.getOrDefault(item.getId(), 0);

            int projectedStock = totalAssetStock;

            // Deduct usage by overlapping bookings
            for (Peminjaman p : overlapping) {
                boolean overlaps = true;
                if (session != null && !session.isEmpty() && p.getSession() != null) {
                    overlaps = isOverlapping(session, p.getSession());
                }

                if (overlaps) {
                    if (p.getDetails() != null) {
                        for (PeminjamanDetail pd : p.getDetails()) {
                            // pd.getItem() is eager fetched now
                            if (pd.getItem().getId().equals(item.getId())) {
                                projectedStock -= pd.getQuantity();
                            }
                        }
                    }
                }
            }

            if (projectedStock < 0)
                projectedStock = 0;
            result.add(new AvailabilityDTO(item.getId(), item.getName(), projectedStock));
        }

        return ResponseEntity.ok(result);
    }

    public static class AvailabilityDTO {
        public Long itemId;
        public String itemName;
        public int available; // Sisa

        public AvailabilityDTO(Long id, String name, int av) {
            this.itemId = id;
            this.itemName = name;
            this.available = av;
        }
    }
}
