-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 08:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flat_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent_flats`
--

CREATE TABLE `agent_flats` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `flat_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `room_picture` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `square_feet` int(11) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `additional_images` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agent_flats`
--

INSERT INTO `agent_flats` (`id`, `agent_id`, `flat_name`, `description`, `location`, `room_picture`, `price`, `bedrooms`, `bathrooms`, `square_feet`, `amenities`, `contact_email`, `contact_phone`, `additional_images`) VALUES
(1, 2, 'Luxury 3BHK Residence in Prime Banani', 'Bright 2,370 sq ft home featuring fire exits, secured entry, maintenance service, and backup generator', 'Banani', 'flat 10 main.jpg', 110000.00, 3, 2, 2370, 'Gas, Generator, Lift, Fire Exits,24/7 Security', 'safaina.khan.cse@ulab.edu.bd', '01818203874', '[\"flat1_bedroom.jpg\",\"flat1_kitchen.jpg\",\"flat1_livingroom.jpg\"]'),
(2, 2, 'Stylish 1700 Sq Ft Apartment in Uttara', 'Modern 2BHK apartment with balconies, parking, lift service, and 24-hour power and water supply', 'Uttara, Dhaka', 'flat4.png', 100000.00, 2, 2, 1700, 'Balcony, Parking, Lift, 24-hour power, 24-hour water supply', 'safaina.khan.cse@ulab.edu.bd', '01818203874', '[\"flat2_livingroom.jpg\",\"flat4_bedroom.jpg\",\"flat2_kitchen.jpg\"]'),
(3, 2, 'Move-In Ready 4-bedroom Flat Near NSU and Evercare – Bashundhara R-A', 'Enjoy a 4-bedroom, 3-bath apartment with balconies, lift, parking, and 24/7 security in Bashundhara R-A', 'Bashundhara R-A', 'flat2.jpg', 220000.00, 4, 3, 2500, 'Wifi, CCTV, Parking, Lift, Spacious Balcony', 'jarin.tasnim.cse@ulab.edu.bd', '01611744381', '[\"flat2_bedroom.jpg\",\"flat2_livingroom.jpg\",\"flat2_kitchen.jpg\"]'),
(6, 2, 'Fully Furnished 3 Bedroom Apartment in Bashundhara R-A', 'Move-in ready flat with bright interiors, CCTV, elevators, and easy connectivity across Bashundhara R-A.', 'Bashundhara R-A, Dhaka', 'flat 6 main.jpg', 300000.00, 3, 3, 2000, 'Wifi, Furnished, 24/7 Security, Elevators, Lift', 'ayush.hassan.cse@ulab.edu.bd', '01867799644', '[\"flat3_bedroom.jpg\",\"flat3_livingroom.jpg\",\"flat2_kitchen.jpg\"]'),
(8, 12, 'Bright & Airy 1400 Sq Ft Apartment in Farmgate', 'Discover comfort with 3 beds, 2 baths, elevators, parking, security, and shopping centers nearby.', 'Monipuripara, Farmgate', '680d4ac3b6740_flat 8 main.jpg', 1000000.00, 3, 2, 1400, 'Elevators, Parking, Security, CCTV', 'tasnim@gmail.com', '01789834538', '[\"680d4ac3b6b16_flat 8 bed.jpg\",\"680d4ac3b6e4f_flat 8 drawing.jpg\",\"680d4ac3b72f3_flat 8 wash.jpg\"]');

-- --------------------------------------------------------

--
-- Table structure for table `flats`
--

CREATE TABLE `flats` (
  `id` int(11) NOT NULL,
  `flat_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `location` text NOT NULL,
  `room_picture` varchar(255) NOT NULL,
  `rent` decimal(10,2) NOT NULL,
  `additional_images` text DEFAULT NULL,
  `bedrooms` int(11) DEFAULT 2,
  `bathrooms` int(11) DEFAULT 1,
  `square_feet` int(11) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `landlord_id` int(11) DEFAULT NULL,
  `status` enum('available','rented') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flats`
--

INSERT INTO `flats` (`id`, `flat_name`, `description`, `location`, `room_picture`, `rent`, `additional_images`, `bedrooms`, `bathrooms`, `square_feet`, `amenities`, `contact_email`, `contact_phone`, `landlord_id`, `status`) VALUES
(1, 'Chic and Modern Apartment Living – 1200 Sq Ft in Gulshan, Dhaka', 'Live comfortably with 2 large bedrooms, lift service, parking, 24/7 electricity, and mall proximity.', 'Gulshan, Dhaka', 'flat2.jpg', 50000.00, '[\"flat2_bedroom.jpg\",\"flat2_kitchen.jpg\",\"flat2_livingroom.jpg\"]', 2, 2, 1200, '[\"Electricity\",\"Gas\",\"Wifi\",\"Parking\",\"Lift\",\"Nearby Malls\"]', 'ayush.hassan.cse@ulab.edu.bd', '01867799644', 8, 'available'),
(2, 'Secure 3BHK Flat with Elevators and Parking in Dhanmondi', 'Fully furnished, secure 3-bedroom apartment minutes from Bangladesh Eye Hospital, Dhanmondi.', 'Dhanmondi', 'flat 8 main.jpg', 25000.00, '[\"flat3_bedroom.jpg\",\"flat3_livingroom.jpg\",\"flat1_kitchen.jpg\"]', 3, 3, 1700, '[\"Wifi\",\"Elevator\",\"Parking\",\"24/7 CCTV\"]', 'jarin.tasnim.cse@ulab.edu.bd', '01611744381', 8, 'available'),
(4, 'Furnished 700 Sq Ft Apartment with Parking in Mohammadpur', '1-bed, 1-bath furnished apartment offering security, fire safety, and close access to schools and malls.', 'Mohammadpur', 'flat3.jpg', 15000.00, '[\"flat1_bedroom.jpg\",\"flat2_livingroom.jpg\",\"flat3_kitchen.jpg\"]', 1, 1, 700, '[\"Wifi\",\"Parking\",\"Nearby Schools and Malls\"]', 'safaina.khan.cse@ulab.edu.bd', '01818203874', 8, 'rented'),
(8, 'Rent a Spacious 2,000 Sq Ft Apartment in Bashundhara R-A', 'Description: Enjoy a 3-bedroom, 3-bath apartment with balconies, lift, parking, and 24/7 security in Bashundhara R-A.', 'Block-C , Bashundhara', 'flat 6 main.jpg', 43210.00, '[\"flat 6 bed.jpeg\",\"flat 6 drawing.jpeg\",\"flat 6 wash.jpeg\"]', 3, 3, 2000, 'Balconies, Lift, Parking, CCTV', 'ayush.hassan.cse@ulab.edu.bd', '01867799644', 10, 'available'),
(9, 'Name: Well-Maintained 1500Sq Ft Flat Available in Banani  ', 'This elegant 2-bedroom flat offers safety features, lifts, electricity, gas, parking, and a premium neighborhood lifestyle.', 'Block A, Banani', 'flat 7 main.png', 22000.00, '[\"flat 7 bed.jpg\",\"flat 7 wash.avif\",\"flat 7 drawing.jpeg\"]', 2, 3, 1500, 'Balconies, CCTV, 24/7 Parking, Lift', 'ayush.hassan.cse@ulab.edu.bd', '01867799644', 10, 'available'),
(10, 'Comfortable Family Home in Mirpur 1650 Sq Ft', 'Spacious living with 3 bedrooms, modern kitchen, fire safety, power backup, and excellent location.', 'Mirpur', '680d4cb59b9cd_flat 9 main.jpeg', 28000.00, '[\"680d4cb59d984_flat 9 drawing.jpeg\",\"680d4cb59dc92_flat 9 wash.avif\",\"680d4cb59df89_flat 9 bed.jpeg\"]', 3, 2, 1650, 'Modern Kitchen, Fire Safety, Power Backup', 'ayush@gmail.com', '01867799644', 10, 'available'),
(11, 'Affordable Flat for Rent in Mohammadpur', 'This secure apartment in Mohammadpur offers wide balconies, lift access, parking, and guard service.', 'Mohammadpur', 'flat 11 main.png', 35000.00, '[\"flat 9 bed.jpeg\",\"flat 9 drawing.jpeg\",\"flat 9 wash.avif\"]', 3, 2, 1200, '[\"Lift\",\"Gas\",\"Current\",\"Balconies\",\"CCTV\"]', 'ayush@gmail.com', '01867799644', 10, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `flat_id` int(11) DEFAULT NULL,
  `month` year(4) NOT NULL,
  `year` year(4) NOT NULL,
  `total_rent` decimal(10,2) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `agreement_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `remaining_due` double DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `tenant_id`, `flat_id`, `month`, `year`, `total_rent`, `amount_paid`, `payment_date`, `user_id`, `agreement_id`, `amount`, `status`, `remaining_due`) VALUES
(56, 3, 4, '2004', '2025', 35000.00, 35000.00, '2025-04-26 20:07:31', 3, 90, 35000.00, 'Paid', 0);

-- --------------------------------------------------------

--
-- Table structure for table `payments_landlord`
--

CREATE TABLE `payments_landlord` (
  `id` int(11) NOT NULL,
  `landlord_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `agreement_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments_landlord`
--

INSERT INTO `payments_landlord` (`id`, `landlord_id`, `flat_id`, `month`, `year`, `total_price`, `user_id`, `agreement_id`, `status`) VALUES
(1, 8, 1, 4, 2025, 110000.00, 8, 13, 'Pending'),
(2, 8, 6, 4, 2025, 300000.00, 8, 14, 'Pending'),
(3, 8, 3, 4, 2025, 220000.00, 8, 15, 'Pending'),
(4, 8, 2, 4, 2025, 100000.00, 8, 16, 'Pending'),
(5, 8, 1, 4, 2025, 110000.00, 8, 17, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE `recommendations` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `recommended_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recommendations`
--

INSERT INTO `recommendations` (`id`, `tenant_id`, `flat_id`, `agent_id`, `recommended_at`) VALUES
(2, 5, 4, 2, '2025-04-25 18:12:38'),
(4, 5, 11, 2, '2025-04-27 17:39:15'),
(5, 3, 4, 2, '2025-04-27 17:42:04');

-- --------------------------------------------------------

--
-- Table structure for table `rental_agreements`
--

CREATE TABLE `rental_agreements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `agreement_date` datetime DEFAULT current_timestamp(),
  `agreement_text` text NOT NULL,
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rental_agreements`
--

INSERT INTO `rental_agreements` (`id`, `user_id`, `flat_id`, `agreement_date`, `agreement_text`, `status`) VALUES
(40, 3, 4, '2025-04-16 21:02:37', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 17:02:37\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(41, 3, 4, '2025-04-16 21:18:43', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 17:18:43\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(42, 3, 4, '2025-04-16 21:19:56', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 17:19:56\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(43, 3, 4, '2025-04-16 21:23:36', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 17:23:36\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(44, 3, 2, '2025-04-16 21:23:55', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-16 17:23:55\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(45, 3, 4, '2025-04-16 21:50:03', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 17:50:03\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(47, 3, 4, '2025-04-16 22:18:15', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 18:18:15\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(48, 3, 1, '2025-04-16 22:19:20', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 2BHK Flat\r\nLocation: Gulshan\r\nRent: BDT 50000.00/month\r\nBedrooms: 2\r\nBathrooms: 2\r\nAgreement Date: 2025-04-16 18:19:20\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(51, 3, 4, '2025-04-16 22:29:50', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 18:29:50\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(52, 3, 4, '2025-04-16 22:37:19', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 18:37:19\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(56, 3, 4, '2025-04-16 22:57:25', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 18:57:25\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(57, 3, 4, '2025-04-16 22:57:48', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 18:57:48\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Agreed'),
(58, 3, 4, '2025-04-16 23:04:27', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 19:04:27\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(60, 3, 4, '2025-04-16 23:08:13', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 19:08:13\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(63, 3, 4, '2025-04-16 23:30:08', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 19:30:08\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(64, 3, 4, '2025-04-16 23:31:15', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 19:31:15\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(65, 3, 4, '2025-04-16 23:35:36', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 19:35:36\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(68, 3, 4, '2025-04-16 23:51:14', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 15000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-16 19:51:14\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(70, 3, 2, '2025-04-22 00:33:43', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-21 20:33:43\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(71, 3, 1, '2025-04-22 00:34:04', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 2BHK Flat\r\nLocation: Gulshan\r\nRent: BDT 50000.00/month\r\nBedrooms: 2\r\nBathrooms: 2\r\nAgreement Date: 2025-04-21 20:34:04\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(72, 3, 4, '2025-04-22 00:39:59', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 24000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-21 20:39:59\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(73, 3, 2, '2025-04-22 01:46:15', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-21 21:46:15\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(74, 3, 1, '2025-04-22 01:49:20', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 2BHK Flat\r\nLocation: Gulshan\r\nRent: BDT 50000.00/month\r\nBedrooms: 2\r\nBathrooms: 2\r\nAgreement Date: 2025-04-21 21:49:20\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(75, 3, 1, '2025-04-22 01:53:23', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 2BHK Flat\r\nLocation: Gulshan\r\nRent: BDT 50000.00/month\r\nBedrooms: 2\r\nBathrooms: 2\r\nAgreement Date: 2025-04-21 21:53:23\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(76, 3, 2, '2025-04-22 01:53:36', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-21 21:53:36\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(77, 3, 2, '2025-04-22 01:56:36', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-21 21:56:36\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(78, 3, 2, '2025-04-22 02:03:19', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-21 22:03:19\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(79, 3, 2, '2025-04-22 02:14:13', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-21 22:14:13\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(80, 3, 2, '2025-04-22 02:16:12', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-21 22:16:12\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(81, 3, 1, '2025-04-22 03:34:44', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 2BHK Flat\r\nLocation: Gulshan, Dhaka\r\nRent: BDT 50000.00/month\r\nBedrooms: 2\r\nBathrooms: 2\r\nAgreement Date: 2025-04-21 23:34:44\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(82, 3, 1, '2025-04-22 03:35:13', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 2BHK Flat\r\nLocation: Gulshan, Dhaka\r\nRent: BDT 50000.00/month\r\nBedrooms: 2\r\nBathrooms: 2\r\nAgreement Date: 2025-04-21 23:35:13\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(83, 3, 2, '2025-04-22 03:35:25', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-21 23:35:25\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(84, 3, 4, '2025-04-26 00:22:15', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 35000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-25 20:22:15\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(85, 3, 2, '2025-04-26 01:00:34', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-25 21:00:34\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(86, 3, 4, '2025-04-26 01:13:08', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 35000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-25 21:13:08\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(87, 3, 2, '2025-04-26 01:16:03', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-25 21:16:03\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(88, 3, 4, '2025-04-26 01:19:40', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 35000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-25 21:19:40\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(89, 3, 2, '2025-04-26 23:53:19', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 3BHK Flat\r\nLocation: Dhanmondi\r\nRent: BDT 25000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-26 19:53:19\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(90, 3, 4, '2025-04-27 00:07:31', '\r\nRental Agreement\r\n------------------------\r\nTenant ID: 3\r\nTenant Name: Jarin Tasnim\r\nFlat: 1BHK\r\nLocation: Mohammadpur\r\nRent: BDT 35000.00/month\r\nBedrooms: 1\r\nBathrooms: 1\r\nAgreement Date: 2025-04-26 20:07:31\r\nBy proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `flat_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 4, 3, 3, 'Good Flat.', '2025-04-13 07:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `sales_agreements`
--

CREATE TABLE `sales_agreements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `agreement_date` datetime DEFAULT current_timestamp(),
  `agreement_text` text NOT NULL,
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_agreements`
--

INSERT INTO `sales_agreements` (`id`, `user_id`, `flat_id`, `agreement_date`, `agreement_text`, `status`) VALUES
(14, 8, 6, '2025-04-22 04:24:33', '\r\nSales Agreement\r\n------------------------\r\nLandlord ID: 8\r\nLandlord Name: abc\r\nFlat: Orieon\r\nLocation: Uttara\r\nPrice: BDT 300000.00/month\r\nBedrooms: 3\r\nBathrooms: 3\r\nAgreement Date: 2025-04-22 00:24:33\r\nBy proceeding, the landlord agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(15, 8, 3, '2025-04-22 04:24:53', '\r\nSales Agreement\r\n------------------------\r\nLandlord ID: 8\r\nLandlord Name: abc\r\nFlat: mh\r\nLocation: Uttara\r\nPrice: BDT 220000.00/month\r\nBedrooms: 4\r\nBathrooms: 3\r\nAgreement Date: 2025-04-22 00:24:53\r\nBy proceeding, the landlord agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(16, 8, 2, '2025-04-22 04:25:16', '\r\nSales Agreement\r\n------------------------\r\nLandlord ID: 8\r\nLandlord Name: abc\r\nFlat: residency\r\nLocation: Bashundhara\r\nPrice: BDT 100000.00/month\r\nBedrooms: 2\r\nBathrooms: 2\r\nAgreement Date: 2025-04-22 00:25:16\r\nBy proceeding, the landlord agrees to the terms and conditions set by Aurora Properties.', 'Pending'),
(17, 8, 1, '2025-04-22 04:25:59', '\r\nSales Agreement\r\n------------------------\r\nLandlord ID: 8\r\nLandlord Name: abc\r\nFlat: asdf\r\nLocation: Banani\r\nPrice: BDT 110000.00/month\r\nBedrooms: 3\r\nBathrooms: 2\r\nAgreement Date: 2025-04-22 00:25:59\r\nBy proceeding, the landlord agrees to the terms and conditions set by Aurora Properties.', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_preferences`
--

CREATE TABLE `tenant_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `preferred_location` varchar(255) DEFAULT NULL,
  `max_rent` decimal(10,2) DEFAULT NULL,
  `min_bedrooms` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_preferences`
--

INSERT INTO `tenant_preferences` (`id`, `user_id`, `preferred_location`, `max_rent`, `min_bedrooms`) VALUES
(2, 5, 'Mohammadpur', 40000.00, 1),
(4, 3, 'Mohammadpur', 25000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tenant','landlord','agent') NOT NULL DEFAULT 'tenant'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'lamia', 'lamia@gmail.com', '$2y$10$OaRo9GXzl3XTe7MBKRmnkeOBvSqN3ERqB/1q4n8L9NSi1r6Q0EYWm', 'admin'),
(2, 'Safaina Khan', 'safaina@gmail.com', '$2y$10$tf5MCy11K8IhFCklL6bsdO11yK/o2IgNJbFNGEPU53Tf08Va13GX2', 'agent'),
(3, 'Jarin Tasnim', 'eara@gmail.com', '$2y$10$q8gQXl0PCqJC.9ThTjpONOZCsVPcsL3l7j9k18yZdOY.MYknBPFdi', 'tenant'),
(5, 'Rakibul', 'rakib@gmail.com', '$2y$10$.wDCXVunzfMitUBgEjub4ul.SMWwkJC5zbPe3GW68TSaD6RP0ZBba', 'tenant'),
(8, 'abc', 'abc@gmail.com', '$2y$10$4JAXOZ1XXHOjFaG409GEQOPXztdNOCF.YsJHe/vL/o/GgmgyBZPBi', 'landlord'),
(10, 'Ayush Hassan', 'ayush@gmail.com', '$2y$10$DgtF9rGsxmkdBKySBat3YuNbEAWR7XUEhnAodZwAIwQ4I2v0EgpbO', 'landlord'),
(12, 'Tasnim Ela', 'tasnim@gmail.com', '$2y$10$shh4.bUJIAmkHNJkGQqzVOzjwS6Scoe1LP6Cu3rpg7FVF6BCJTH.2', 'agent');

-- --------------------------------------------------------

--
-- Table structure for table `user_favourites`
--

CREATE TABLE `user_favourites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_favourites`
--

INSERT INTO `user_favourites` (`id`, `user_id`, `flat_id`) VALUES
(1, 4, 4),
(5, 3, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agent_flats`
--
ALTER TABLE `agent_flats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flats`
--
ALTER TABLE `flats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_landlord` (`landlord_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_payment_per_month` (`tenant_id`,`flat_id`,`month`,`year`),
  ADD KEY `flat_id` (`flat_id`);

--
-- Indexes for table `payments_landlord`
--
ALTER TABLE `payments_landlord`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rental_agreements`
--
ALTER TABLE `rental_agreements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `flat_id` (`flat_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flat_id` (`flat_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sales_agreements`
--
ALTER TABLE `sales_agreements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tenant_preferences`
--
ALTER TABLE `tenant_preferences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_favourites`
--
ALTER TABLE `user_favourites`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agent_flats`
--
ALTER TABLE `agent_flats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `flats`
--
ALTER TABLE `flats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `payments_landlord`
--
ALTER TABLE `payments_landlord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rental_agreements`
--
ALTER TABLE `rental_agreements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales_agreements`
--
ALTER TABLE `sales_agreements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tenant_preferences`
--
ALTER TABLE `tenant_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flats`
--
ALTER TABLE `flats`
  ADD CONSTRAINT `fk_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`id`);

--
-- Constraints for table `rental_agreements`
--
ALTER TABLE `rental_agreements`
  ADD CONSTRAINT `rental_agreements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rental_agreements_ibfk_2` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
