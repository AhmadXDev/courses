import 'package:flutter/material.dart';

extension Nav on BuildContext {
  void push(Widget target) {
    Navigator.push(
      this,
      MaterialPageRoute(
        builder: (context) => target,
      ),
    );
  }
}
