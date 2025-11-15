import 'package:supabase_flutter/supabase_flutter.dart';

class Database {
  final supabase = Supabase.instance.client;

  signup({required String email, required String password}) async {
    await supabase.auth.signUp(password: password, email: email);
  }

  login({required String email, required String password}) async {
    await supabase.auth.signInWithPassword(password: password, email: email);
  }
}
