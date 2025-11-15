// ignore: avoid_relative_lib_imports
import 'pages/weeks/week2/day3/lib/pages/home_page.dart';
import 'package:flutter/material.dart';

void main() {
  // Api().getData();
  runApp(const MainApp());
}

class MainApp extends StatelessWidget {
  const MainApp({super.key});

  @override
  Widget build(BuildContext context) {
    return const MaterialApp(home: HomePage());
  }
}
