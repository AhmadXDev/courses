import 'package:flutter/material.dart';
import 'package:flutter_course/pages/weeks/week1/day5/page2.dart';

void main() {
  runApp(const MainApp());
}

class MainApp extends StatelessWidget {
  const MainApp({super.key});

  @override
  Widget build(BuildContext context) {
    return const MaterialApp(home: Page2());
  }
}
