import 'package:flutter/material.dart';
import './page1.dart';

class Page2 extends StatelessWidget {
  const Page2({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.grey,
        title: const Text("Welcome"),
      ),
      body: Padding(
        padding: const EdgeInsets.all(8.0),
        child: Container(
          width: 300,
          height: 300,
          color: Colors.blue.shade300,
          padding: const EdgeInsets.only(top: 20, left: 10),
          child: Column(
            children: [
              const Text(
                "title",
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 40,
                  fontWeight: FontWeight.normal,
                ),
              ),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  const Icon(Icons.face),
                  ElevatedButton(
                      onPressed: () {
                        Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const Page1(),
                            ));
                      },
                      child: const Text("move"))
                ],
              )
            ],
          ),
        ),
      ),
    );
  }
}
