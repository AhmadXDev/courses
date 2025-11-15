import 'package:flutter/material.dart';

class FirstPage extends StatelessWidget {
  const FirstPage({super.key});
  
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          centerTitle: true,
          title: const Text("QUIZ 1"),
          backgroundColor: Colors.blue.shade200,
        ),
        body: Column(
          children: [
            Container(
              height: 120,
              width: 200,
              color: Colors.red.shade300,
              child: const Text("Faisal"),
            ),
            Container(
              height: 120,
              width: 200,
              color: Colors.yellow.shade300,
              child: const Text("CSS"),
            ),
            const Text("Welcome")
          ],
        ));
  }
}
