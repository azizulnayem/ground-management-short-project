<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch gallery images and descriptions
$query = "SELECT image_url, description FROM gallery";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .gallery-container {
            display: flex;  /* Change to flexbox for centering */
            flex-wrap: wrap;
            justify-content: center; /* Center items */
            gap: 20px;
        }
        .gallery-item {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            width: 100%;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            cursor: pointer; /* Indicate clickable item */
        }
        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .gallery-item img {
            width: 100%;
            height: 200px; /* Fixed height for uniformity */
            object-fit: cover; /* Maintain aspect ratio */
            display: block;
            border-bottom: 2px solid #3498db;
        }
        .gallery-description {
            padding: 10px;
            color: #555;
            text-align: left;
            font-size: 16px;
        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.8); /* Black background with transparency */
        }
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%; /* Responsive */
            height: auto; /* Maintain aspect ratio */
            border: 1px solid #fff;
            border-radius: 8px;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }
        @media (max-width: 600px) {
            .modal-content {
                max-width: 90%; /* Use 90% on smaller screens */
            }
        }
        .button {
            display: inline-block;
            background-color: #0044cc;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #002b80;
        }
    </style>
</head>
<body>
<a href="index.php" class="button"><i class="fas fa-house"></i> Homepage</a>
<a href="auth.php" class="button"><i class="fas fa-user"></i> Login</a>
<h2>Gallery</h2>

<div class="gallery-container">
    <?php while ($image = $result->fetch_assoc()): ?>
        <div class="gallery-item" onclick="openModal(this)">
            <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Gallery Image">
            <div class="gallery-description">
                <p><?php echo htmlspecialchars($image['description']); ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Modal Structure -->
<div id="myModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
    <div id="modalDescription" class="gallery-description"></div>
</div>

<script>
function openModal(element) {
    const img = element.getElementsByTagName("img")[0];
    const description = element.getElementsByClassName("gallery-description")[0].innerHTML;

    // Set the image source in the modal
    const modalImage = document.getElementById("modalImage");
    modalImage.src = img.src;
    
    // Open the modal
    const modal = document.getElementById("myModal");
    modal.style.display = "block";

    // Set the modal description
    document.getElementById("modalDescription").innerHTML = description;

    // When the image is clicked, request full screen
    modalImage.onclick = function() {
        if (modalImage.requestFullscreen) {
            modalImage.requestFullscreen();
        } else if (modalImage.mozRequestFullScreen) { // Firefox
            modalImage.mozRequestFullScreen();
        } else if (modalImage.webkitRequestFullscreen) { // Chrome, Safari, and Opera
            modalImage.webkitRequestFullscreen();
        } else if (modalImage.msRequestFullscreen) { // IE/Edge
            modalImage.msRequestFullscreen();
        }
    }
}

function closeModal() {
    document.getElementById("myModal").style.display = "none";
}

// Close modal when clicking anywhere outside of it
window.onclick = function(event) {
    const modal = document.getElementById("myModal");
    if (event.target === modal) {
        closeModal();
    }
}
</script>

</body>
</html>
