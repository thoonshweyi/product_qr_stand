-- Branch import generated from rdsbranches.csv with addresses from rebranches.csv
START TRANSACTION;

INSERT INTO `branches` (`branch_name`, `branch_code`, `branch_short_name`, `branch_address`, `branch_phone_no`, `status_id`, `user_id`, `created_at`, `updated_at`, `erp_branch_id`)
VALUES
    ('Head Office', 'MM-001', 'HO', 'No.76, Lanthit Street, Near Arleing Ngar Sint Pagoda, Insein Township, Yangon, Myanmar', NULL, NULL, 1, NOW(), NOW(), 7),
    ('Lanthit', 'MM-101', 'LAN1', 'No.76, Lanthit Street, Near Arleing Ngar Sint Pagoda, Insein Township, Yangon, Myanmar', NULL, NULL, 1, NOW(), NOW(), 1),
    ('Theik Pan', 'MM-102', 'MDY1', 'Ma.8/6, Theik Pan Rd, Bet: 62 && 63 St., Chanmyathazi Tsp., Mandalay, Myanmar', NULL, NULL, 1, NOW(), NOW(), 2),
    ('Satsan', 'MM-103', 'SAT1', 'No.5 Upper Pazundaung Road, Satsan, Mingalar Taung Nyunt Tsp, Yangon, Myanmar', NULL, NULL, 1, NOW(), NOW(), 3),
    ('East Dagon', 'MM-104', 'EDG1', 'No.(1/ka), No(2) Main Road, 15Qts, Near School of Nursing and Mitwifery, East Dagon Tsp, Yangon, Myanmar', NULL, NULL, 1, NOW(), NOW(), 9),
    ('Mawlamyine', 'MM-105', 'MLM1', 'No.(70), Corner of Upper Main Road and A Lal Tan St, Maung Ngan Qr (Kha Pa Ya Compound), Mawlamyine', NULL, NULL, 1, NOW(), NOW(), 10),
    ('Tampawady', 'MM-106', 'MDY2', 'No.(489/490), Between Lanthit Street & Corner of Shwe San Kaing Pagoda, Inside of Kha Pa Ya, Tapawadi Quarter, Chanmyatharzi Tsp, Mandalay.', NULL, NULL, 1, NOW(), NOW(), 11),
    ('Hlaingtharyar', 'MM-107', 'HTY1', 'No(4 to 5), Corner between Yangon-Pathein & Yangon-Ton Tay St, Infront of AGE Industrial, Inside Padan, Hlaingtharyar Tsp, Yangon.', NULL, NULL, 1, NOW(), NOW(), 19),
    ('Ayetharyar', 'MM-108', 'ATY1', 'No.35 , 5 Quarter , Ayetharyar Township , Taunggyi Shan State', NULL, NULL, 1, NOW(), NOW(), 21),
    ('Bago', 'MM-110', 'BGO1', 'Ward.8, Oakthar Myo Thit, Bago Township, Bago, Myanmar', NULL, NULL, 1, NOW(), NOW(), 23),
    ('PRO1 PLUS (Terminal M)', 'MM-112', 'PTMN1', 'No.196, 1st Floor, Terminal M Shopping Mall, No.3 Highway, Yangon Industrial Zone, Mingalardon Township, Yangon.', NULL, NULL, 1, NOW(), NOW(), 27),
    ('South Dagon', 'MM-113', 'SDG1', 'No. 523, Corner of Industrial Zone Rd & Pinlon Rd, Ward 23, South Dagon Tsp., Yangon, Myanmar', NULL, NULL, 1, NOW(), NOW(), 28),
    ('Da Nyin Gone', 'MM-114', 'SPT1', 'No.103-104 Bayint Naung Road, Shwe Pyi Thar Industrial Zone (4)', NULL, NULL, 1, NOW(), NOW(), 30),
    ('Project Sales', 'MM-201', 'PRJ1', NULL, NULL, NULL, 1, NOW(), NOW(), 12),
    ('Online Sales', 'MM-202', 'ONS1', NULL, NULL, NULL, 1, NOW(), NOW(), 18),
    ('Outlet Sales', 'MM-203', 'OLS1', NULL, NULL, NULL, 1, NOW(), NOW(), 20),
    ('Clearance Sale', 'MM-205', 'OLS2', 'No.5 Upper Pazundaung Road, Satsan, Mingalar Taung Nyunt Tsp, Yangon, Myanmar', NULL, NULL, 1, NOW(), NOW(), 26),
    ('WH-Myo Houng', 'MM-504', 'WHMH', 'U Pwar Gone bridge ,between Sa-Lae Monestary & Myow Hyow station, Commercial Agricultural Warehouse (or)Warehouse No , (14,15,8,9), Chan Mya Tharzi tp, Mandalay', NULL, NULL, 1, NOW(), NOW(), 8),
    ('WH-Mingalardon', 'MM-505', 'WHMLD', 'No(58, 61), Zaygabar Compound, Corner of Kayaypin St & Naung Yan St, Mingalardon Industrial Zone, Yangon', NULL, NULL, 1, NOW(), NOW(), 13),
    ('DC-Myawaddy', 'MM-509', 'DCMWD', 'No(69), Ever Green Company Compound, Main Road, Myawaddy Trading Zone, Myawaddy', NULL, NULL, 1, NOW(), NOW(), 17),
    ('DC-Mingalardon2', 'MM-510', 'DCMLD2', 'No(58, 61), Zaygabar Compound, Corner of Kayaypin St & Naung Yan St, Mingalardon Industrial Zone, Yangon', NULL, NULL, 1, NOW(), NOW(), 29),
    ('DC-Mingalardon3', 'MM-511', 'DC-3', 'No(58, 61), Zaygabar Compound, Corner of Kayaypin St & Naung Yan St, Mingalardon Industrial Zone, Yangon', NULL, NULL, 1, NOW(), NOW(), 31),
    ('Mingalardon', 'MM-109', 'MLD1', 'No(58, 61), Zaygabar Compound, Corner of Kayaypin St & Naung Yan St, Mingalardon Industrial Zone, Yangon', NULL, NULL, 1, NOW(), NOW(), 22),
    ('Nay Pyi Taw', 'MM-115', 'NPT1', 'No.12+23, Bawgathiri Ah Sint Myint Motor Repair Work Shop, Nay Pyi Taw', NULL, NULL, 1, NOW(), NOW(), 32);

COMMIT;
