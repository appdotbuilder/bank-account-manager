import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

interface Props {
    auth: {
        user?: {
            name: string;
            email: string;
            role: string;
        };
    };
    [key: string]: unknown;
}

export default function Welcome({ auth }: Props) {
    return (
        <>
            <Head title="SecureBank - Professional Banking Management" />
            
            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100">
                {/* Navigation */}
                <nav className="bg-white shadow-sm border-b">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between items-center h-16">
                            <div className="flex items-center space-x-3">
                                <div className="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <span className="text-white font-bold text-lg">🏦</span>
                                </div>
                                <span className="text-xl font-bold text-gray-900">SecureBank</span>
                            </div>
                            
                            <div className="flex items-center space-x-4">
                                {auth.user ? (
                                    <Link
                                        href="/dashboard"
                                        className="text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <div className="flex items-center space-x-4">
                                        <Link
                                            href="/login"
                                            className="text-gray-600 hover:text-gray-900 font-medium"
                                        >
                                            Login
                                        </Link>
                                        <Link
                                            href="/register"
                                            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                                        >
                                            Get Started
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Hero Section */}
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="text-center">
                        <h1 className="text-5xl font-bold text-gray-900 mb-6">
                            🏦 Professional Bank Account Management
                        </h1>
                        <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                            Complete banking solution with role-based access, advanced account management, 
                            secure transactions, and comprehensive financial controls.
                        </p>
                        
                        {!auth.user && (
                            <div className="flex justify-center space-x-4">
                                <Button asChild size="lg" className="text-lg px-8 py-3">
                                    <Link href="/register">Start Banking Today</Link>
                                </Button>
                                <Button asChild variant="outline" size="lg" className="text-lg px-8 py-3">
                                    <Link href="/login">Sign In</Link>
                                </Button>
                            </div>
                        )}
                    </div>
                    
                    {/* Features Grid */}
                    <div className="mt-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {/* Role-Based Access */}
                        <div className="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                            <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                                <span className="text-2xl">👥</span>
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 mb-4">Multi-Role System</h3>
                            <ul className="text-gray-600 space-y-2">
                                <li>• <strong>Administrators:</strong> Full system control</li>
                                <li>• <strong>Operators:</strong> Account management</li>
                                <li>• <strong>Customers:</strong> Personal banking</li>
                            </ul>
                        </div>

                        {/* Account Management */}
                        <div className="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                            <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                                <span className="text-2xl">💳</span>
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 mb-4">Smart Account Types</h3>
                            <ul className="text-gray-600 space-y-2">
                                <li>• Customizable balance limits</li>
                                <li>• Transaction restrictions</li>
                                <li>• Auto-dormant detection</li>
                                <li>• Automatic account closure</li>
                            </ul>
                        </div>

                        {/* Advanced Features */}
                        <div className="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                            <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                                <span className="text-2xl">⚡</span>
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 mb-4">Advanced Controls</h3>
                            <ul className="text-gray-600 space-y-2">
                                <li>• Hold amount management</li>
                                <li>• Scheduled auto-debits</li>
                                <li>• Account blocking/unblocking</li>
                                <li>• Comprehensive audit trails</li>
                            </ul>
                        </div>

                        {/* Security */}
                        <div className="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                            <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                                <span className="text-2xl">🔒</span>
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 mb-4">Enterprise Security</h3>
                            <ul className="text-gray-600 space-y-2">
                                <li>• Role-based permissions</li>
                                <li>• Transaction authorization</li>
                                <li>• Account ownership locks</li>
                                <li>• Secure data encryption</li>
                            </ul>
                        </div>

                        {/* Transactions */}
                        <div className="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                            <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                                <span className="text-2xl">💸</span>
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 mb-4">Smart Transactions</h3>
                            <ul className="text-gray-600 space-y-2">
                                <li>• Instant transfers</li>
                                <li>• Daily transaction limits</li>
                                <li>• Fee management</li>
                                <li>• Real-time balance updates</li>
                            </ul>
                        </div>

                        {/* Monitoring */}
                        <div className="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                            <div className="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                                <span className="text-2xl">📊</span>
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 mb-4">Complete Oversight</h3>
                            <ul className="text-gray-600 space-y-2">
                                <li>• Real-time dashboards</li>
                                <li>• Transaction monitoring</li>
                                <li>• Account status tracking</li>
                                <li>• Automated notifications</li>
                            </ul>
                        </div>
                    </div>

                    {/* User Status Display */}
                    {auth.user && (
                        <div className="mt-16 bg-blue-50 rounded-xl p-8 text-center border border-blue-200">
                            <div className="flex items-center justify-center space-x-3 mb-4">
                                <div className="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span className="text-white font-bold">
                                        {auth.user.name.charAt(0).toUpperCase()}
                                    </span>
                                </div>
                                <div>
                                    <h3 className="text-xl font-bold text-gray-900">Welcome back, {auth.user.name}!</h3>
                                    <p className="text-blue-600 font-medium capitalize">
                                        {auth.user.role} Access Level
                                    </p>
                                </div>
                            </div>
                            
                            <div className="flex justify-center space-x-4">
                                <Button asChild size="lg">
                                    <Link href="/dashboard">Go to Dashboard</Link>
                                </Button>
                                {auth.user.role === 'customer' && (
                                    <Button asChild variant="outline" size="lg">
                                        <Link href="/bank-accounts">My Accounts</Link>
                                    </Button>
                                )}
                            </div>
                        </div>
                    )}

                    {/* Call to Action for Non-Authenticated Users */}
                    {!auth.user && (
                        <div className="mt-16 bg-gray-900 rounded-xl p-12 text-center text-white">
                            <h2 className="text-3xl font-bold mb-4">Ready to Get Started?</h2>
                            <p className="text-xl text-gray-300 mb-8">
                                Join thousands of users who trust SecureBank for their banking needs.
                            </p>
                            <div className="flex justify-center space-x-4">
                                <Button asChild size="lg" className="bg-white text-gray-900 hover:bg-gray-100">
                                    <Link href="/register">Create Account</Link>
                                </Button>
                                <Button asChild variant="outline" size="lg" className="border-white text-white hover:bg-white hover:text-gray-900">
                                    <Link href="/login">Sign In</Link>
                                </Button>
                            </div>
                        </div>
                    )}
                </div>
                
                {/* Footer */}
                <footer className="bg-gray-900 text-white py-12 mt-20">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                        <div className="flex justify-center items-center space-x-3 mb-4">
                            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span className="text-white font-bold">🏦</span>
                            </div>
                            <span className="text-xl font-bold">SecureBank</span>
                        </div>
                        <p className="text-gray-400">
                            Professional banking management system built with Laravel and React.
                        </p>
                        <p className="text-sm text-gray-500 mt-4">
                            © 2024 SecureBank. All rights reserved.
                        </p>
                    </div>
                </footer>
            </div>
        </>
    );
}