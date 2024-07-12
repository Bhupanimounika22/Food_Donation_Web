<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Donation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .left-half {
            width: 50%;
        }

        .right-half {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        input, select {
            border: none;
            border-bottom: 1px solid #a0aec0; /* Gray color */
            outline: none;
        }
        input:focus, select:focus {
            border-bottom: 2px solid #805ad5; /* Purple color */
        } 
        .quantity-field, .persons-field {
            display: none;
        }
    </style>
</head>

<body class="flex justify-center items-center h-screen">
    <div class="flex w-full justify-center">
        <div class="flex-1 bg-purple-200 h-screen flex items-center justify-center">
            <img src="https://media.istockphoto.com/id/1284244967/photo/closeup-of-word-donation-made-from-white-rice-and-small-piles-of-rice-buckwheat-and-millet.webp?b=1&s=170667a&w=0&k=20&c=IKLfrE213RqlN5qIkzmFXfr6hZOXqqO3-z0la6fj2yA=" alt="Donation Image" class="w-auto h-full ">
        </div>
        <div class="flex-1 bg-white p-8 rounded-lg shadow-lg max-w-full ml-8">
            <h1 class="text-2xl font-bold mb-4 text-center text-purple-700">Food Donation Form</h1>
            <form action="http://spammers4.rf.gd/" method="POST" class="flex flex-col items-center">
                <div class="mb-4 w-full">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name of Donor:</label>
                    <input type="text" id="name" name="name" class="mt-1 p-2 w-full required">
                </div>
                <div class="mb-4 w-full">
                    <label for="food_type" class="block text-sm font-medium text-gray-700">Food Type:</label>
                    <select id="food_type" name="food_type" class="mt-1 p-2 w-full required" onchange="toggleFields()">
                        <option value="" selected disabled>Select Food Type</option>
                        <option value="raw_food">Raw Food</option>
                        <option value="cooked_food">Cooked Food</option>
                    </select>
                </div>
                <div class="mb-4 quantity-field w-full">
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity:</label>
                    <input type="text" id="quantity" name="quantity" class="mt-1 p-2 w-full required">
                </div>
                <div class="mb-4 w-full">
                    <label for="food_details" class="block text-sm font-medium text-gray-700">Specify Food in Detail:</label>
                    <input type="text" id="food_details" name="food_details" class="mt-1 p-2 w-full required">
                </div>
                
                <div class="mb-4 w-full">
                    <label for="expiry_time" class="block text-sm font-medium text-gray-700">Expiry Time:</label>
                    <input type="text" id="expiry_time" name="expiry_time" class="mt-1 p-2 w-full required">
                </div>
                <div class="mb-4 persons-field w-full">
                    <label for="cooked_food_quantity" class="block text-sm font-medium text-gray-700">Enough for no of persons:</label>
                    <input type="text" id="cooked_food_quantity" name="no_of" class="mt-1 p-2 w-full required">
                </div>
                <div class="mb-4 w-full">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address:</label>
                    <input type="text" id="address" name="address" class="mt-1 p-2 w-full required">
                </div>
                <div class="mb-4 w-full">
                    <label for="contact" class="block text-sm font-medium text-gray-700">Contact No:</label>
                    <input type="text" id="contact" name="contact" class="mt-1 p-2 w-full required">
                </div>
                <div class="mt-4 w-full flex justify-center">
                    <button type="submit" class="bg-purple-500 text-white px-80 py-2 rounded-md w-300 hover:bg-purple-700">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFields() {
            var foodType = document.getElementById("food_type").value;
            var quantityField = document.querySelector(".quantity-field");
            var personsField = document.querySelector(".persons-field");

            if (foodType === "raw_food") {
                quantityField.style.display = "block";
                personsField.style.display = "none";
            } else if (foodType === "cooked_food") {
                quantityField.style.display = "none";
                personsField.style.display = "block";
            }
        }
    </script>
</body>

</html>