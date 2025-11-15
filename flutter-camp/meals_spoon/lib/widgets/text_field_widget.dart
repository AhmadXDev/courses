import 'package:flutter/material.dart';

class TextFieldWidget extends StatefulWidget {
  final String text;
  final TextEditingController controller;
  final bool isPassword;
  const TextFieldWidget({
    super.key,
    this.isPassword = false,
    required this.text,
    required this.controller,
  });

  @override
  State<TextFieldWidget> createState() => _TextFieldWidgetState();
}

class _TextFieldWidgetState extends State<TextFieldWidget> {
  bool isObscureText = false;
  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: widget.controller,
      obscureText: isObscureText,
      decoration: InputDecoration(
        labelText: widget.text,
        suffixIcon: widget.isPassword
            ?  InkWell(
              onTap: () {
                isObscureText = !isObscureText;
                setState(() {});
              },
                child:  Icon(isObscureText ? Icons.visibility : Icons.visibility_off),
              )
            : null,
        border: const OutlineInputBorder(
          borderRadius: BorderRadius.all(
            Radius.circular(16),
          ),
        ),
      ),
    );
  }
}
