package SQLHandling;

import java.net.ConnectException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class ConnectionHandler {
    private static String databaseURL = "jdbc:mysql://localhost/kino"; //+ "&useSSL=true" + "&requireSSL=true"; //causes unknown database error
    private static Connection conn;

    public static Connection connect() throws ConnectException {
        try {
            conn = DriverManager.getConnection(databaseURL, LoginDataProvider.user, LoginDataProvider.pass);
        } catch (SQLException e) {
            throw new ConnectException();
        }
        return conn;
    }
}