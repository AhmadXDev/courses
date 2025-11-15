import 'package:flutter/material.dart';

class CalculatorPage extends StatelessWidget {
  const CalculatorPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Calculator"),
        centerTitle: true,
        backgroundColor: const Color.fromARGB(255, 150, 22, 22),
      ),
      body: Column(
        children: [
          Container(
            height: 100,
            width: double.infinity,
            color: const Color.fromARGB(255, 99, 97, 97),
            alignment: Alignment.centerRight,
            child: const Text(
              "0",
              style: TextStyle(fontSize: 40),
            ),
          ),
          buttonRow("7", "8", "9"),
          buttonRow("4", "5", "6"),
          buttonRow("1", "2", "3"),
          buttonRow("0", "C", "=")
        ],
      ),
    );
  }

  Widget button(String text) {
    return Expanded(
        child: Container(
      height: 80,
      margin: const EdgeInsets.all(8),
      color: const Color.fromARGB(255, 17, 119, 202),
      alignment: Alignment.center,
      child: Text(
        text,
        style: const TextStyle(fontSize: 30, fontWeight: FontWeight.bold),
      ),
    ));
  }

  Row buttonRow(String button1, String button2, String button3) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: [button(button1), button(button2), button(button3)],
    );
  }
}
