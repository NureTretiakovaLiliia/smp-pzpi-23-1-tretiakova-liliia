<?php

function readProductsFromFile(string $filename): array {
    $products = [];
    if (!file_exists($filename)) {
        return $products;
    }

    $file = fopen($filename, 'r');
    fgetcsv($file);

    while (($data = fgetcsv($file)) !== false) {
        $id = (int)$data[0];
        $name = $data[1];
        $price = (int)$data[2];
        $products[$id] = ['name' => $name, 'price' => $price];
    }

    fclose($file);
    return $products;
}

function writeProductsToFile(string $filename, array $products): void {
    $file = fopen($filename, 'w');

    fputcsv($file, ['id', 'name', 'price']);

    foreach ($products as $id => $product) {
        fputcsv($file, [$id, $product['name'], $product['price']]);
    }

    fclose($file);
}

function writeCartToFile(string $filename, array $cart): void {
    $file = fopen($filename, 'w');
    fputcsv($file, ['id', 'amount']);
    foreach ($cart as $id => $amount) {
        fputcsv($file, [$id, $amount]);
    }
    fclose($file);
}

function readCartFromFile(string $filename): array {
    $cart = [];
    if (!file_exists($filename)) {
        return $cart;
    }

    $file = fopen($filename, 'r');
    fgetcsv($file); 

    while (($data = fgetcsv($file)) !== false) {
        $id = (int)$data[0];
        $amount = (int)$data[1];
        $cart[$id] = $amount;
    }

    fclose($file);
    return $cart;
}


function printMenu($isFirst) {
    if ($isFirst == true) {
        echo "\n################################\n";
        echo "# ПРОДОВОЛЬЧИЙ МАГАЗИН \"ВЕСНА\" #\n";
        echo "################################\n";
    }

    echo "1 Вибрати товари\n";
    echo "2 Отримати підсумковий рахунок\n";
    echo "3 Налаштувати свій профіль\n";
    echo "0 Вийти з програми\n";
}


function padRight($string, $length) {
    $padLength = $length - mb_strlen($string);
    return $string . str_repeat(' ', max(0, $padLength));
}


function printProducts() {
    global $products;

    echo "№  НАЗВА                   ЦІНА\n";

    foreach ($products as $id => $product) {
        $numStr = str_pad($id, 3, ' ', STR_PAD_RIGHT);
        $nameStr = padRight($product['name'], 24);
        $priceStr = str_pad($product['price'], 3, ' ', STR_PAD_LEFT);
        echo "$numStr$nameStr$priceStr\n";
    }
    echo "   -----------\n";
    echo "0  ПОВЕРНУТИСЯ\n";
}


function buyProducts() {
    global $products;
    global $cart;
    if (!isset($cart)) {
        $cart = [];
    }

    while (true) {
        printProducts();

        $id = readline("Виберіть товар: ");

        if ($id == 0) {
            break;
        } else if (!array_key_exists($id, $products)) {
            echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n";
        } else {
            echo "Вибрано: " . $products[$id]['name'] . "\n";

            $amount = readline("Введіть кількість, штук: ");

            if (!is_numeric($amount) || $amount < 0 || $amount >= 100) {
                echo "ПОМИЛКА! Кількість має бути від 0 до 99\n";
                continue;
            }

            addItemToCart($id, $amount);
            writeCartToFile("cart.csv", $cart);

            seeCart();
        }
    }
}


function addItemToCart($itemId, $amount) {
    global $cart;

    if ($amount == 0 && isset($cart[$itemId])) {
        echo "ВИДАЛЯЮ З КОШИКА\n";
        unset($cart[$itemId]);
    } else if ($amount > 0) {
        $cart[$itemId] = $amount;
    }
}


function seeCart() {
    global $cart;
    global $products;

    if (empty($cart)) {
        echo "КОШИК ПОРОЖНІЙ\n";
    } else {
        echo "У КОШИКУ:\n";
        echo "НАЗВА                   КІЛЬКІСТЬ\n";
        foreach ($cart as $itemId => $amount) {
            $nameStr = padRight($products[$itemId]['name'], 24);
            echo $nameStr . $amount . "\n";
        }
    }
}


function checkout() {
    global $cart;
    global $products;

    $total = 0;

    echo "№  НАЗВА                 ЦІНА  КІЛЬКІСТЬ  ВАРТІСТЬ\n";
    $counter = 1;
    foreach ($cart as $itemId => $amount) {
        $price = $products[$itemId]['price'];
        $name = $products[$itemId]['name'];
        $sum = $amount * $price;
        $total += $sum;

        $nameStr = padRight($name, 22);
        $counterStr = str_pad($counter, 3, ' ', STR_PAD_RIGHT);
        $priceStr = str_pad($price, 5, ' ', STR_PAD_LEFT);
        $amountStr = str_pad($amount, 8, ' ', STR_PAD_LEFT);
        $sumStr = str_pad($sum, 8, ' ', STR_PAD_LEFT);
        echo "$counterStr$nameStr$priceStr$amountStr$sumStr\n";
        $counter++;
    }
    echo "РАЗОМ ДО CПЛАТИ: " . $total . "\n\n";
}

function updateProfile() {
    while (true) {
        $name = readLine("Ваше імʼя: ");
        if (preg_match('/[А-ЩЬЮЯЄІЇҐа-щьюяєіїґa-zA-Z]/u', $name)) {
            $profile['name'] = $name;
            break;
        } else {
            echo "ПОМИЛКА! Імʼя повинно містити хоча б одну літеру\n";
        }
    }

    while (true) {
        $age = readLine("Ваш вік: ");
        if (is_numeric($age) && $age >= 7 && $age <= 150) {
            $profile['age'] = (int)$age;
            break;
        } else {
            echo "ПОМИЛКА! Вік повинен бути від 7 до 150\n";
        }
    }

    echo "Ваше ім'я: " . $profile['name'] . "\n";
    echo "Ваш вік: " . $profile['age'] . "\n\n";
}


$products = readProductsFromFile("products.csv");

$profile = [
    "name" => "",
    "age" => ""
];

$cart = readCartFromFile("cart.csv");

$isFirst = true;

while (true) {
    printMenu($isFirst);
    $isFirst = false;

    $choice = readline("Введіть команду: ");

    switch ($choice) {
        case 0:
            writeCartToFile("cart.csv", $cart);
            exit();
            break;
        case 1:
            buyProducts();
            break;
        case 2:
            checkout();
            break;
        case 3:
            updateProfile();
            break;    
        default:
            echo "ПОМИЛКА! Введіть правильну команду\n";
            break;
    }

}
