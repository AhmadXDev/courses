import 'package:flutter/material.dart';
import 'package:meals_list/pages/welcome_page.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    WidgetsApp.debugAllowBannerOverride = false;
    return const MaterialApp(home: WelcomePage());
  }
}
